<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin',
            'nim'      => '0000000000',
            'email'    => 'admin@sipcacuk.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Mahasiswa
        User::create([
            'name'     => 'Test Mahasiswa',
            'nim'      => '1234567890',
            'email'    => 'mahasiswa@sipcacuk.com',
            'password' => Hash::make('mahasiswa123'),
            'role'     => 'mahasiswa',
        ]);
    }
}