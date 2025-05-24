<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BidangPenelitianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('d_bidang_penelitian')->insert([
            ['bidang' => 'Kecerdasan Buatan'],
            ['bidang' => 'Rekayasa Perangkat Lunak'],
            ['bidang' => 'Jaringan Komputer'],
            ['bidang' => 'Sistem Informasi'],
            ['bidang' => 'Keamanan Siber'],
        ]);
    }
}
