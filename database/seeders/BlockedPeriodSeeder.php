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
        
        if ($menus->count() > 0) {
            BlockedPeriod::factory(8)->forSpecificMenu()->create();
            BlockedPeriod::factory(3)->maintenanceHours()->forAllMenus()->create();
            BlockedPeriod::factory(5)->shortBreak()->forSpecificMenu()->create();
        }
    }
}