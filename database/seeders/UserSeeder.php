<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ketua RW
        User::create([
            'name' => 'Ketua RW 07',
            'email' => 'rw@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ketua_rw',
        ]);

        // Admin
        User::create([
            'name' => 'Admin RW 07',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // User Sample
        User::create([
            'name' => 'Warga Contoh',
            'email' => 'warga@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
