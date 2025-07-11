<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(4),
            'required_time' => fake()->numberBetween(30, 120),
            'price' => fake()->randomFloat(2, 1000, 10000),
            'description' => fake()->optional()->paragraph(),
            'photo_path' => 'https://picsum.photos/640/480?random=' . fake()->randomNumber(),
            'display_order' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(80),
        ];
    }
}