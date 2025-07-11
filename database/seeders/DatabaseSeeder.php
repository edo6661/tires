<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BusinessSettingSeeder::class,
            MenuSeeder::class,
            ReservationSeeder::class,
            TireStorageSeeder::class,
            ContactSeeder::class,
            PaymentSeeder::class,
            BlockedPeriodSeeder::class,
            AnnouncementSeeder::class,
            FaqSeeder::class,
            QuestionnaireSeeder::class,
        ]);
    }
}