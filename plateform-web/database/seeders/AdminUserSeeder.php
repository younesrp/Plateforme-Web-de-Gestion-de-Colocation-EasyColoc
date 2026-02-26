<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@easycoloc.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'reputation' => 100,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@easycoloc.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'reputation' => 50,
            'email_verified_at' => now(),
        ]);
    }
}
