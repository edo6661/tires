<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\UserRole;
use App\Enums\Gender;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'full_name' => fake()->name(),
            'full_name_kana' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'company_name' => fake()->optional()->company(),
            'department' => fake()->optional()->jobTitle(),
            'company_address' => fake()->optional()->address(),
            'home_address' => fake()->optional()->address(),
            'date_of_birth' => fake()->optional()->date(),
            'gender' => fake()->optional()->randomElement(Gender::values()),
            'role' => fake()->randomElement(UserRole::values()),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}