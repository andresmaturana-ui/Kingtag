<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los "Toy" de las fotos (el contrario de "King"). Los "King" siguen en photo_likes.
     */
    public function up(): void
    {
        Schema::create('photo_toys', function (Blueprint $table) {
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['photo_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_toys');
    }
};
