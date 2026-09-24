<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $text = strtoupper(fake()->unique()->lexify('????'));

        return [
            'text' => $text,
            'normalized' => Tag::normalize($text),
        ];
    }
}
