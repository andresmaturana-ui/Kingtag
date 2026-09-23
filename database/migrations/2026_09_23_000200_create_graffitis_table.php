<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Una pieza física en un punto de la ciudad. Varios registros del mismo
        // tag en el mismo lugar se juntan en una sola fila.
        Schema::create('graffitis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('photo');
            $table->string('thumb');
            $table->unsignedInteger('reports')->default(1);
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graffiti_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->string('thumb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
        Schema::dropIfExists('graffitis');
    }
};
