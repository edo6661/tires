<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $announcements = [
                [
                    'is_active' => true,
                    'published_at' => now(),
                    'translations' => [
                        'en' => [
                            'title' => 'Welcome to Our New Online Reservation System',
                            'content' => 'We are excited to announce the launch of our new online reservation system. You can now book appointments for tire services, oil changes, and tire storage easily through our website.',
                        ],
                        'ja' => [
                            'title' => '新しいオンライン予約システムへようこそ',
                            'content' => '新しいオンライン予約システムの開始をお知らせいたします。ウェブサイトから簡単にタイヤサービス、オイル交換、タイヤ保管のご予約ができるようになりました。',
                        ],
                    ],
                ],
                [
                    'is_active' => true,
                    'published_at' => now()->subDays(7),
                    'translations' => [
                        'en' => [
                            'title' => 'Extended Business Hours During Winter Season',
                            'content' => 'Starting December 1st, we will be extending our business hours to better serve our customers during the busy winter tire season. New hours will be Monday-Friday 8:00 AM - 7:00 PM, Saturday 8:00 AM - 6:00 PM.',
                        ],
                        'ja' => [
                            'title' => '冬季シーズン中の営業時間延長',
                            'content' => '12月1日より、冬タイヤシーズンの繁忙期にお客様により良いサービスを提供するため、営業時間を延長いたします。新しい営業時間：月曜-金曜 8:00-19:00、土曜 8:00-18:00',
                        ],
                    ],
                ],
                [
                    'is_active' => true,
                    'published_at' => now()->subDays(14),
                    'translations' => [
                        'en' => [
                            'title' => 'Tire Storage Service Now Available',
                            'content' => 'We now offer professional tire storage services. Keep your seasonal tires in perfect condition with our climate-controlled storage facility. Contact us for more information.',
                        ],
                        'ja' => [
                            'title' => 'タイヤ保管サービス開始',
                            'content' => '専門的なタイヤ保管サービスを開始いたします。温度管理された保管施設で、季節用タイヤを完璧な状態で保管いたします。詳細についてはお問い合わせください。',
                        ],
                    ],
                ],
            ];

            foreach ($announcements as $announcementData) {
                $translations = $announcementData['translations'];
                unset($announcementData['translations']);

                $announcement = Announcement::create($announcementData);

                // Simpan translations
                $translationRecords = [];
                foreach ($translations as $locale => $translationData) {
                    $translationRecords[] = array_merge($translationData, [
                        'announcement_id' => $announcement->id,
                        'locale' => $locale,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('announcement_translations')->insert($translationRecords);
            }
        });
    }
}