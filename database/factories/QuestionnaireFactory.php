<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;

class QuestionnaireFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'questions_and_answers' => [
                'vehicle_type' => fake()->randomElement(['sedan', 'suv', 'truck', 'compact']),
                'tire_condition' => fake()->randomElement(['good', 'fair', 'poor']),
                'preferred_time' => fake()->randomElement(['morning', 'afternoon', 'evening']),
                'additional_services' => fake()->optional()->sentence(),
            ],
        ];
    }
}