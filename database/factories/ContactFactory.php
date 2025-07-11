<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Enums\ContactStatus;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->optional()->randomElement([null, User::factory()]),
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->optional()->phoneNumber(),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(ContactStatus::values()),
            'admin_reply' => fake()->optional()->paragraph(),
            'replied_at' => fake()->optional()->dateTime(),
        ];
    }
}