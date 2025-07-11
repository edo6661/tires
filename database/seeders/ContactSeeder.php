<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\User;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Contact::factory(20)->create();

        if ($users->count() > 0) {
            Contact::factory(10)->create([
                'user_id' => $users->random()->id,
            ]);
        }
    }
}