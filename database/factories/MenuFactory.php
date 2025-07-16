<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class MenuFactory extends Factory
{
    public function definition(): array
    {
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6B7280'];
        
        return [
            'name' => fake()->sentence(4),
            'required_time' => fake()->numberBetween(30, 120),
            'price' => fake()->randomFloat(2, 1000, 10000),
            'description' => fake()->optional()->paragraph(),
            'photo_path' => 'https://picsum.photos/640/480?random=' . fake()->randomNumber(),
            'display_order' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(80),
            'color' => fake()->randomElement($colors),
        ];
    }
}