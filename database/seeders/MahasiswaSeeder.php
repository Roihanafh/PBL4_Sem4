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
        $data = [
            [
                'mhs_nim'        => '2341720226',
                'user_id'        => 3,
                'full_name'      => 'Ramadhani Bi Hayyin',
                'alamat'         => 'Jl. Kembang Kertas',
                'telp'           => '081333537649',
                'prodi_id'       => 1, // disesuaikan dengan migration
                'status_magang'  => 'Belum Magang',
            ]
        ];
        DB::table('m_mahasiswa')->insert($data);
        }
    }

