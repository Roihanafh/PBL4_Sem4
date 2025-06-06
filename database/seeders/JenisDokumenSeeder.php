<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'jenis_dokumen_id' => 1,
                'nama' => 'Surat Pengantar',
                'default' => 1,
            ],
            [
                'jenis_dokumen_id' => 2,
                'nama_jenis_dokumen' => 'Surat Keterangan Magang',
                'default' => 1,
            ],
            [
                'jenis_dokumen_id' => 3,
                'nama_jenis_dokumen' => 'Laporan Magang',
                'default' => 0,
            ],
        ];

        DB::table('m_jenis_dokumen')->insert($data);
    }
}
