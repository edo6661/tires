<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Menu;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $menus = Menu::all();

        if ($users->count() > 0 && $menus->count() > 0) {
            Reservation::factory(30)->create([
                'user_id' => $users->random()->id,
                'menu_id' => $menus->random()->id,
            ]);
        }
    }
}