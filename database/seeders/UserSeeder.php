<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun 1: Super Admin
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Dinar',
                'password' => 'password123',
                'role'     => 'superadmin',
            ]
        );

        // Akun 2: Admin
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name'     => 'Staff Admin',
                'password' => 'password123',
                'role'     => 'admin',
            ]
        );
    }
}
