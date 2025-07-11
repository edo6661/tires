<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Reservation;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reservations = Reservation::all();

        if ($users->count() > 0) {
            Payment::factory(25)->create([
                'user_id' => $users->random()->id,
                'reservation_id' => $reservations->count() > 0 ? $reservations->random()->id : null,
            ]);
        }
    }
}