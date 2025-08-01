<?php
namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            
            BusinessSetting::query()->delete();
            DB::table('business_setting_translations')->truncate();

            
            $settingData = [
                'phone_number' => '03-1234-5678',
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

                'site_public' => true,
                'reply_email' => 'info@tirepro.co.id',
                'google_analytics_id' => 'GA-XXXXXXXXX-X',
                'top_image_path' => null,
            ];

            
            $translations = [
                'en' => [
                    'shop_name' => 'Tire Pro Service',
                    'access_information' => 'Near Shibuya Station, exit from the east gate and walk for 5 minutes.',
                    'address' => '1-1-1 Shibuya, Shibuya-ku, Tokyo 150-0002',
                    'site_name' => 'Tire Pro Service',
                    'shop_description' => 'We are a professional tire service provider with over 10 years of experience.',
                    'terms_of_use' => 'By using our services, you agree to comply with the applicable terms and conditions.',
                    'privacy_policy' => 'We respect your privacy and are committed to protecting the personal information you provide to us.',
                ],
                'ja' => [
                    'shop_name' => 'タイヤプロサービス',
                    'address' => '東京都渋谷区渋谷1-1-1 〒150-0002',
                    'access_information' => '渋谷駅の近く、東口から出て徒歩5分です。',
                    'site_name' => 'タイヤプロサービス',
                    'shop_description' => '私たちは10年以上の経験を持つプロのタイヤサービスプロバイダーです。',
                    'terms_of_use' => '当社のサービスをご利用になることにより、適用される利用規約に従うことに同意したことになります。',
                    'privacy_policy' => '私たちはお客様のプライバシーを尊重し、ご提供いただいた個人情報の保護に努めています。',
                ],
            ];

            
            $setting = BusinessSetting::create($settingData);

            
            $setting->setTranslations($translations);
        });
    }
}