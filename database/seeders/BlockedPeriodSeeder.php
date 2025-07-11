<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlockedPeriod;
use App\Models\Menu;

class BlockedPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $menus = Menu::all();

        BlockedPeriod::factory(10)->create();

        if ($menus->count() > 0) {
            BlockedPeriod::factory(5)->create([
                'menu_id' => $menus->random()->id,
                'all_menus' => false,
            ]);
        }
    }
}