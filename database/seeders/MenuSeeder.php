<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $menus = [
                [
                    'required_time' => 50,
                    'price' => 3000.00,
                    'display_order' => 1,
                    'is_active' => true,
                    'color' => '#3B82F6',
                    'translations' => [
                        'en' => [
                            'name' => 'Installation of tires purchased at our store',
                            'description' => 'Professional installation of tires purchased at our store',
                        ],
                        'ja' => [
                            'name' => '当店でご購入いただいたタイヤの取り付け',
                            'description' => '当店でご購入いただいたタイヤの専門的な取り付けサービス',
                        ],
                    ],
                ],
                [
                    'required_time' => 50,
                    'price' => 4000.00,
                    'display_order' => 2,
                    'is_active' => true,
                    'color' => '#10B981',
                    'translations' => [
                        'en' => [
                            'name' => 'Replacement and installation of tires brought in',
                            'description' => 'Installation of tires shipped directly to our store',
                        ],
                        'ja' => [
                            'name' => '持ち込みタイヤの交換・取り付け',
                            'description' => '当店に直送されたタイヤの取り付けサービス',
                        ],
                    ],
                ],
                [
                    'required_time' => 40,
                    'price' => 2500.00,
                    'display_order' => 3,
                    'is_active' => true,
                    'color' => '#F59E0B',
                    'translations' => [
                        'en' => [
                            'name' => 'Oil change',
                            'description' => 'Complete oil change service',
                        ],
                        'ja' => [
                            'name' => 'オイル交換',
                            'description' => '完全なオイル交換サービス',
                        ],
                    ],
                ],
                [
                    'required_time' => 40,
                    'price' => 3500.00,
                    'display_order' => 4,
                    'is_active' => true,
                    'color' => '#EF4444',
                    'translations' => [
                        'en' => [
                            'name' => 'Tire storage and tire replacement',
                            'description' => 'Tire storage service and replacement',
                        ],
                        'ja' => [
                            'name' => 'タイヤ保管・タイヤ交換',
                            'description' => 'タイヤ保管サービスと交換',
                        ],
                    ],
                ],
                [
                    'required_time' => 30,
                    'price' => 2000.00,
                    'display_order' => 5,
                    'is_active' => true,
                    'color' => '#8B5CF6',
                    'translations' => [
                        'en' => [
                            'name' => 'Change tires by bringing your own',
                            'description' => 'Tire changing service for customer-provided tires',
                        ],
                        'ja' => [
                            'name' => '持ち込みタイヤ交換',
                            'description' => 'お客様ご提供のタイヤ交換サービス',
                        ],
                    ],
                ],
            ];

            foreach ($menus as $menuData) {
                $translations = $menuData['translations'];
                unset($menuData['translations']);

                $menu = Menu::create($menuData);

                
                $translationRecords = [];
                foreach ($translations as $locale => $translationData) {
                    $translationRecords[] = array_merge($translationData, [
                        'menu_id' => $menu->id,
                        'locale' => $locale,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('menu_translations')->insert($translationRecords);
            }
        });
    }
}