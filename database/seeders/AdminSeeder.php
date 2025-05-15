<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = DB::table('m_users')->where('username', 'admin')->first();

        if ($adminUser) {
            DB::table('m_admin')->insert([
                'user_id' => $adminUser->user_id,
                'nama'    => 'Admin Sistem',
                'email'   => 'admin@gmail.com',
                'telp'    => '08123456789',
            ]);
        }
    }
}
