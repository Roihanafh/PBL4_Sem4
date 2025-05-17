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
                'keterangan' => 'Mahasiswa mengirim lamaran magang.',
                'waktu' => now()->subDays(5),
            ],
        ];
        DB::table('t_log_aktivitas_mhs')->insert($data);
    }
}
