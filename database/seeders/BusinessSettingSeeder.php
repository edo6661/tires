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
                'phone_number' => '04-2937-5296', // diganti sesuai baru
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
                    'access_information' => 'Near Iruma Miyadera, about 5 minutes by car from Iruma IC.',
                    'address' => '2095-8 Miyadera, Iruma-shi, Saitama 358-0014, Japan', // diganti baru
                    'site_name' => 'Tire Pro Service',
                    'shop_description' => 'We are a professional tire service provider with over 10 years of experience.',
                    'terms_of_use' => 'By using our services, you agree to comply with the applicable terms and conditions.',
                    'privacy_policy' => 'We respect your privacy and are committed to protecting the personal information you provide to us.',
                ],
                'ja' => [
                    'shop_name' => 'タイヤプロサービス',
                    'address' => '〒358-0014 埼玉県入間市宮寺2095-8', // diganti baru
                    'access_information' => '入間市宮寺の近く、入間ICから車で約5分です。',
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
