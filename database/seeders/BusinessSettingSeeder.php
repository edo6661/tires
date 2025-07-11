<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessSetting;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        BusinessSetting::factory()->create([
            'shop_name' => 'Tire Pro Service',
            'phone_number' => '03-1234-5678',
            'address' => '1-1-1 Shibuya, Shibuya-ku, Tokyo 150-0002',
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '09:00', 'close' => '17:00'],
                'sunday' => ['closed' => true],
            ],
            'site_public' => true,
        ]);
    }
}