<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TireStorage;
use App\Models\User;

class TireStorageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() > 0) {
            TireStorage::factory(15)->create([
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
