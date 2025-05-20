<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LowonganMagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'judul' => 'Magang Front-End Developer',
                'deskripsi' => 'Bekerja sebagai pengembang antarmuka pengguna web.',
                'tanggal_mulai_magang' => now()->addDays(10),
                'deadline_lowongan' => now()->addDays(5),
                'lokasi' => 'Yogyakarta',
                'perusahaan_id' => 1,
                'periode_id' => 1,
                'sylabus_path' => 'sylabus/frontend.pdf',
            ],
            [
                'judul' => 'Magang Data Analyst',
                'deskripsi' => 'Analisis data perusahaan dan visualisasi.',
                'tanggal_mulai_magang' => now()->addDays(20),
                'deadline_lowongan' => now()->addDays(10),
                'lokasi' => 'Jakarta',
                'perusahaan_id' => 1,
                'periode_id' => 1,
                'sylabus_path' => 'sylabus/data_analyst.pdf',
            ],
            [
                'judul' => 'Magang Mobile Developer',
                'deskripsi' => 'Pengembangan aplikasi Android menggunakan Kotlin.',
                'tanggal_mulai_magang' => now()->addDays(15),
                'deadline_lowongan' => now()->addDays(7),
                'lokasi' => 'Bandung',
                'perusahaan_id' => 2,
                'periode_id' => 2,
                'sylabus_path' => 'sylabus/mobile_dev.pdf',
            ],
        ];
        DB::table('t_lowongan_magang')->insert($data);
    }
}
