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
            'email' => 'admin@gmail.com',
            'full_name' => 'Admin User',
            'role' => UserRole::ADMIN,
            'password' => bcrypt('admin123'), 
        ]);

        User::factory()->create([
            'email' => 'customer@gmail.com',
            'full_name' => 'Customer User',
            'role' => UserRole::CUSTOMER,
            'password' => bcrypt('customer123'),
        ]);

        User::factory(20)->create();
    }
}