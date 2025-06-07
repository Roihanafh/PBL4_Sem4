<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrefrensiLokasiMahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 't_prefrensi_lokasi_mahasiswa';

    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
    ];
}
