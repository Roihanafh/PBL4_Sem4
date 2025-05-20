<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LamaranMagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'mhs_nim' => '2341720226',
                'lowongan_id' => 1,
                'tanggal_lamaran' => now(),
                'status' => 'pending',
            ],
        ];
        DB::table('t_lamaran_magang')->insert($data);
    }
}
