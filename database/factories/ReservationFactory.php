<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Menu;
use App\Enums\ReservationStatus;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_number' => 'RSV' . fake()->unique()->numerify('######'),
            'user_id' => User::factory(),
            'menu_id' => Menu::factory(),
            'reservation_datetime' => fake()->dateTimeBetween('now', '+1 month'),
            'number_of_people' => fake()->numberBetween(1, 8),
            'amount' => fake()->randomFloat(2, 1000, 20000),
            'status' => fake()->randomElement(ReservationStatus::values()),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}