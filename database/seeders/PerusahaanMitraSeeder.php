<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'PT Teknologi Nusantara',
                'alamat' => 'Jl. Merdeka No. 88, Jakarta',
                'email' => 'info@teknologinusantara.co.id',
                'telp' => '021-5551234',
            ],
            [
                'nama' => 'CV Solusi Digital',
                'alamat' => 'Jl. Taman Siswa No. 12, Yogyakarta',
                'email' => 'contact@solusidigital.co.id',
                'telp' => '0274-789456',
            ],
        ];
        DB::table('m_perusahaan_mitra')->insert($data);
    }
}
