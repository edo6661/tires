<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_name' => fake()->company(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'access_information' => fake()->optional()->paragraph(),
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '09:00', 'close' => '17:00'],
                'sunday' => ['closed' => true],
            ],
            'website_url' => fake()->optional()->url(),
            'site_name' => fake()->optional()->domainName(),
            'shop_description' => fake()->optional()->paragraph(),
            'top_image_path' => 'https://picsum.photos/640/480?random=' . fake()->randomNumber(),
            'site_public' => fake()->boolean(),
            'reply_email' => fake()->optional()->safeEmail(),
            'terms_of_use' => fake()->optional()->paragraphs(3, true),
            'privacy_policy' => fake()->optional()->paragraphs(3, true),
            'google_analytics_id' => fake()->optional()->uuid(),
        ];
    }
}