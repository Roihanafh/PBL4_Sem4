<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LamaranMagangModel extends Model
{
    use HasFactory;
    protected $table = 't_lamaran_magang';
    protected $primaryKey = 'lamaran_id';
    public $timestamps = false;

    protected $fillable = [
        'mhs_nim',
        'lowongan_id',
        'tanggal_lamaran',
        'status',
        'dosen_id'
    ];

    protected $casts = [
        'tanggal_lamaran' => 'datetime',
    ];

    /**
     * Relasi ke dokumen-dokumen (d_dokumen)
     */
    public function dokumen()
    {
        return $this->hasMany(DokumenModel::class, 'lamaran_id', 'lamaran_id');
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganModel::class, 'lowongan_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(NotifikasiModel::class, 'lamaran_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function dosen()
    {
        return $this->hasOneThrough(
            DosenPembimbingModel::class,
            LowonganModel::class,
            'lowongan_id', // foreign key di lowongan
            'dosen_id',    // foreign key di dosen
            'lowongan_id', // foreign key di lamaran
            'dosen_id'     // foreign key di lowongan
        );
    }

}
