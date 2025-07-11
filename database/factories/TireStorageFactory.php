<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Enums\TireStorageStatus;

class TireStorageFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Bridgestone', 'Michelin', 'Yokohama', 'Dunlop', 'Toyo', 'Continental'];
        $sizes = ['185/65R15', '195/65R15', '205/65R16', '215/60R16', '225/55R17', '235/50R18'];
        
        return [
            'user_id' => User::factory(),
            'tire_brand' => fake()->randomElement($brands),
            'tire_size' => fake()->randomElement($sizes),
            'storage_start_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'planned_end_date' => fake()->dateTimeBetween('now', '+6 months'),
            'storage_fee' => fake()->randomFloat(2, 5000, 20000),
            'status' => fake()->randomElement(TireStorageStatus::values()),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}