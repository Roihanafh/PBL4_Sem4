<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeahlianMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listBidangKeahlian = DB::table('m_bidang_keahlian')->pluck('id');
        $listMahasiswaNIM = DB::table('m_mahasiswa')->pluck('mhs_nim');

        foreach ($listMahasiswaNIM as $nim) {
            for ($i = 0; $i < 2; $i++) { // langsung pakai angka, contoh: 2 minat per mahasiswa
                DB::table('t_minat_mahasiswa')->insert([
                    'mhs_nim' => $nim,
                    'bidang_keahlian_id' => $listBidangKeahlian->random(),
                ]);
            }
        }
    }
}
