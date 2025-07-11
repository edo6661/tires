<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Menu;

class BlockedPeriodFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = fake()->dateTimeBetween($startDate, '+1 day');
        
        return [
            'menu_id' => fake()->optional()->randomElement([null, Menu::factory()]),
            'start_datetime' => $startDate,
            'end_datetime' => $endDate,
            'reason' => fake()->sentence(),
            'all_menus' => fake()->boolean(30),
        ];
    }
}