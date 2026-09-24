<?php

namespace Database\Factories;

use App\Models\Graffiti;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Graffiti>
 */
class GraffitiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::factory(),
            // Alrededor del centro de Santiago.
            'lat' => -33.4489 + fake()->randomFloat(5, -0.05, 0.05),
            'lng' => -70.6693 + fake()->randomFloat(5, -0.05, 0.05),
            'photo' => 'photos/demo.jpg',
            'thumb' => 'photos/demo_t.jpg',
        ];
    }
}
