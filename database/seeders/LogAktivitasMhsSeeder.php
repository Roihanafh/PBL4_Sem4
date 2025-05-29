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
<<<<<<< HEAD
=======
            'keterangan' => 'Mahasiswa mengirim lamaran magang',
            'waktu' => now()->subDays(13)
        ],
        [
            'lamaran_id' => 1,
>>>>>>> 16ca67020666036955a19242956f36aac289a16a
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
<<<<<<< HEAD
            'waktu' => now()->subDays(3)->setTime(10, 15, 0), // 2025-05-21 10:15:00 WIB
     ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mengikuti pelatihan penggunaan API perusahaan',
            'waktu' => now()->subDays(2)->setTime(13, 0, 0), // 2025-05-22 13:00:00 WIB
       ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membantu debugging kode aplikasi web',
            'waktu' => now()->subDay()->setTime(14, 20, 0), // 2025-05-23 14:20:00 WIB
       ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membuat laporan harian perkembangan proyek',
            'waktu' => now()->setTime(11, 0, 0), // 2025-05-24 11:00:00 WIB
       ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Mengikuti meeting dengan tim developer',
            'waktu' => now()->addDay()->setTime(9, 30, 0), // 2025-05-25 09:30:00 WIB
       ],
        [
            'lamaran_id' => 1,
            'keterangan' => 'Menguji fitur baru pada aplikasi mobile',
            'waktu' => now()->addDays(2)->setTime(15, 10, 0), // 2025-05-26 15:10:00 WIB
       ],
=======
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
>>>>>>> 16ca67020666036955a19242956f36aac289a16a
        [
            'lamaran_id' => 1,
            'keterangan' => 'Membantu update dokumentasi teknis sistem',
            'waktu' => now()->addDays(3)
        ],
    ];
    DB::table('t_log_aktivitas_mhs')->insert($data);
}
}
