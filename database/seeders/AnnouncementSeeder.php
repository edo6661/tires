<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to Our New Online Reservation System',
                'content' => 'We are excited to announce the launch of our new online reservation system. You can now book appointments for tire services, oil changes, and tire storage easily through our website.',
                'is_active' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Extended Business Hours During Winter Season',
                'content' => 'Starting December 1st, we will be extending our business hours to better serve our customers during the busy winter tire season. New hours will be Monday-Friday 8:00 AM - 7:00 PM, Saturday 8:00 AM - 6:00 PM.',
                'is_active' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Tire Storage Service Now Available',
                'content' => 'We now offer professional tire storage services. Keep your seasonal tires in perfect condition with our climate-controlled storage facility. Contact us for more information.',
                'is_active' => true,
                'published_at' => now()->subDays(14),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::factory()->create($announcement);
        }

    }
}
