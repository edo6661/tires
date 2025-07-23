<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        BusinessSetting::factory()->create([
            'shop_name' => 'Tire Pro Service',
            'phone_number' => '03-1234-5678',
            'address' => '1-1-1 Shibuya, Shibuya-ku, Tokyo 150-0002',
            'access_information' => 'Near Shibuya Station, exit from the east gate and walk for 5 minutes.',
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '09:00', 'close' => '17:00'],
                'sunday' => ['closed' => true],
            ],
            'website_url' => 'https://fts.biz.id',
            'site_name' => 'Tire Pro Service',
            'shop_description' => 'We are a professional tire service provider with over 10 years of experience. We offer a wide range of services including tire replacement, balancing, alignment, and seasonal tire storage.',
            'site_public' => true,
            'reply_email' => 'info@tirepro.co.id',
            'terms_of_use' => 'By using our services, you agree to comply with the applicable terms and conditions. Please read them carefully before making a reservation.',
            'privacy_policy' => 'We respect your privacy and are committed to protecting the personal information you provide to us.',
            'google_analytics_id' => 'GA-XXXXXXXXX-X',
            'top_image_path' => null,
        ]);
    }
}
