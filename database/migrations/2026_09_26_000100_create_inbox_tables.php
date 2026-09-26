<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La bandeja "Mis mensajes": lo que el admin le escribe a cada usuario
     * (y sus respuestas), los avisos a todos, y los King y Toy que reciben.
     */
    public function up(): void
    {
        // Un aviso enviado a todos. Cada usuario recibe su propia copia en inbox_messages.
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // La conversación entre TAGKING (los admins) y un usuario.
        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('from_admin');
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('broadcast_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'id']);
        });

        // "Fulano le dio King (o Toy) a tu foto".
        Schema::create('vote_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 4);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_notices');
        Schema::dropIfExists('inbox_messages');
        Schema::dropIfExists('broadcasts');
    }
};
