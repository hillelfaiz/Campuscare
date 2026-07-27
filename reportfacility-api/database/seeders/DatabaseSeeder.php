<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Admin
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@campuscare.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat Akun Satpam
        User::create([
            'nama' => 'Satpam',
            'email' => 'satpam@campuscare.id',
            'password' => Hash::make('password'),
            'role' => 'satpam',
        ]);
    }
}
