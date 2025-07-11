<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'is_active' => fake()->boolean(80),
            'published_at' => fake()->optional()->dateTime(),
        ];
    }
}
