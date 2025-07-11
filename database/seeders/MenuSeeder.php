<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Installation of tires purchased at our store',
                'required_time' => 50,
                'price' => 3000.00,
                'description' => 'Professional installation of tires purchased at our store',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Replacement and installation of tires brought in (tires shipped directly to our store)',
                'required_time' => 50,
                'price' => 4000.00,
                'description' => 'Installation of tires shipped directly to our store',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Oil change',
                'required_time' => 40,
                'price' => 2500.00,
                'description' => 'Complete oil change service',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Tire storage and tire replacement at our store',
                'required_time' => 40,
                'price' => 3500.00,
                'description' => 'Tire storage service and replacement',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Change tires by bringing your own (removal and removal of season tires, etc.)',
                'required_time' => 30,
                'price' => 2000.00,
                'description' => 'Tire changing service for customer-provided tires',
                'display_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::factory()->create($menu);
        }

    }
}
