<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('m_users')->where('username', 'dosen')->first();

        if ($user) {
            DB::table('m_dosen')->insert([
                'user_id' => $user->user_id,
                'nama'    => 'Dr. Siti Dosen, M.Kom',
                'email'   => 'siti.dosen@gmail.com',
                'telp'    => '081298765432',
            ]);
        }
    }
}
