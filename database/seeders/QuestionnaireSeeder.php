<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\Reservation;

class QuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::all();

        if ($reservations->count() > 0) {
            // Create questionnaires for some reservations
            $selectedReservations = $reservations->random(min(15, $reservations->count()));
            
            foreach ($selectedReservations as $reservation) {
                Questionnaire::factory()->create([
                    'reservation_id' => $reservation->id,
                    'questions_and_answers' => [
                        'vehicle_make' => fake()->randomElement(['Toyota', 'Honda', 'Nissan', 'Mazda', 'Subaru']),
                        'vehicle_model' => fake()->word(),
                        'vehicle_year' => fake()->numberBetween(2010, 2024),
                        'current_tire_condition' => fake()->randomElement(['good', 'fair', 'poor', 'needs_replacement']),
                        'preferred_tire_brand' => fake()->randomElement(['Bridgestone', 'Michelin', 'Yokohama', 'Dunlop', 'Toyo']),
                        'additional_services_needed' => fake()->optional()->sentence(),
                        'special_instructions' => fake()->optional()->paragraph(),
                    ],
                ]);
            }
        }
    }
}