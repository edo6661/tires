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
        if (User::count() > 0 && Menu::count() > 0) {
            Reservation::factory(30)->withValidMenuAndDateTime()->create();
        }
    }
}
