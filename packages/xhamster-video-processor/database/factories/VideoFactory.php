<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Iashchak\XhamsterVideoProcessor\Models\Video;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->unique()->safeEmail(),
            'path' => fake()->unique()->safeUrl(),
        ];
    }
}