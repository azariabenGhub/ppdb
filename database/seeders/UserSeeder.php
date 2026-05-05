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
         // Admin / Kepala Sekolah
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'kepala_sekolah',
        ]);

        // Panitia 1
        User::create([
            'name' => 'Panitia PPDB',
            'email' => 'panitia@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'panitia',
        ]);
    }
}
