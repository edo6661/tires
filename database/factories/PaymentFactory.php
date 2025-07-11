<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Reservation;
use App\Enums\PaymentStatus;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reservation_id' => fake()->optional()->randomElement([null, Reservation::factory()]),
            'amount' => fake()->randomFloat(2, 1000, 20000),
            'payment_method' => fake()->randomElement(['credit_card', 'bank_transfer', 'cash']),
            'status' => fake()->randomElement(PaymentStatus::values()),
            'transaction_id' => fake()->optional()->uuid(),
            'payment_details' => [
                'card_last_four' => fake()->numerify('####'),
                'card_brand' => fake()->randomElement(['Visa', 'MasterCard', 'JCB']),
            ],
            'paid_at' => fake()->optional()->dateTime(),
        ];
    }
}