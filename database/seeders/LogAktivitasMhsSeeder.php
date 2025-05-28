<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogAktivitasMhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $data = [
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mahasiswa mengirim lamaran magang',
            'waktu' => now()->subDays(13)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mahasiswa diterima untuk magang di perusahaan',
            'waktu' => now()->subDays(9)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Hari pertama magang: Orientasi tim IT',
            'waktu' => now()->subDays(4)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membantu setup database untuk proyek internal',
            'waktu' => now()->subDays(3)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mengikuti pelatihan penggunaan API perusahaan',
            'waktu' => now()->subDays(2)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membantu debugging kode aplikasi web',
            'waktu' => now()->subDay()
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membuat laporan harian perkembangan proyek',
            'waktu' => now()
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mengikuti meeting dengan tim developer',
            'waktu' => now()->addDay()
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Menguji fitur baru pada aplikasi mobile',
            'waktu' => now()->addDays(2)
        ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membantu update dokumentasi teknis sistem',
            'waktu' => now()->addDays(3)
        ],
    ];
    DB::table('t_log_aktivitas_mhs')->insert($data);
}
}
