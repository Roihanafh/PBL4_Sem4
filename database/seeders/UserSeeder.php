<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('12345'),
                'level_id' => 1, // Admin
            ],
            [
                'username' => 'dosen',
                'password' => Hash::make('dsn'),
                'level_id' => 2, // Dosen
            ],
            [
                'username' => 'mahasiswa',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],
        ]);
    }
}
