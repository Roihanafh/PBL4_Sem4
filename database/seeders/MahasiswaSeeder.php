<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('m_users')->where('username', 'mahasiswa')->first();
        // Ambil program studi pertama
        $prodi = DB::table('m_program_studi')->first();

        if ($user && $prodi) {
            DB::table('m_mahasiswa')->insert([
                'mhs_nim'        => '2341720226',
                'user_id'        => $user->user_id,
                'full_name'      => 'Ramadhani Bi Hayyin',
                'alamat'         => 'Jl. Kembang Kertas',
                'telp'           => '081333537649',
                'prodi_id'       => $prodi->prodi_id,
                'status_magang'  => 'Belum Magang',
            ]);
        }
    }
}
