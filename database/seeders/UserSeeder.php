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
            'full_name_kana' => 'カスタマー ユーザー',
            'role' => UserRole::CUSTOMER,
            'password' => bcrypt('customer123'),
            'phone_number' => '1234567890',
        ]);

        User::factory(20)->create();
    }
}
