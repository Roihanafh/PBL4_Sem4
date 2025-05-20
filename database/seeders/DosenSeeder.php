<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
                'user_id' => 2,
                'nama'    => 'Dr. Siti Dosen, M.Kom',
                'email'   => 'siti.dosen@gmail.com',
                'telp'    => '081298765432',
        ];
        DB::table('m_dosen')->insert($data);
        }
    }

