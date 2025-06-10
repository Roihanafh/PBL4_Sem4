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

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function desa()
    {
        return $this->belongsTo(DesaModel::class, 'desa_id', 'desa_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(KecamatanModel::class, 'kecamatan_id', 'kecamatan_id');
    }
    
    public function kabupaten()
    {
        return $this->belongsTo(KabupatenModel::class, 'kabupaten_id', 'kabupaten_id');
    }

    public function provinsi()
    {
        return $this->belongsTo(ProvinsiModel::class, 'provinsi_id', 'provinsi_id');
    }
}