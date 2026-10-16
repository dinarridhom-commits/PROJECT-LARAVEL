<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun 1: Super Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Dinar',
                'password' => Hash::make('password123'),
                'role'     => 'superadmin',
            ]
        );

        // Akun 2: Admin
        User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name'     => 'Staff Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );
    }
}
