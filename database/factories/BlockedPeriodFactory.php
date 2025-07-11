<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Menu;

class BlockedPeriodFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+2 months');
        $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(1, 8) . ' hours');

        $allMenus = fake()->boolean(30);

        return [
            'menu_id' => $allMenus ? null : Menu::inRandomOrder()->value('id') ?? Menu::factory(),
            'start_datetime' => $startDate,
            'end_datetime' => $endDate,
            'reason' => fake()->sentence(),
            'all_menus' => $allMenus,
        ];
    }
}
