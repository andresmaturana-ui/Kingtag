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
        // Un tag es la firma de un artista ("KASE"). Puede existir sin dueño
        // hasta que el artista lo reclama con su cuenta.
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('text', 60);
            $table->string('normalized', 60)->unique();
            $table->foreignId('artist_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
