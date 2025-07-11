<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'full_name' => 'Admin User',
            'role' => UserRole::ADMIN,
        ]);

        User::factory()->create([
            'email' => 'customer@example.com',
            'full_name' => 'Customer User',
            'role' => UserRole::CUSTOMER,
        ]);

        // Create additional users
        User::factory(20)->create();
    }
}