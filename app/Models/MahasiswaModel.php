<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaModel extends Model
{
    use HasFactory;

    protected $table      = 'm_mahasiswa';
    protected $primaryKey = 'mhs_nim';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'mhs_nim',
        'user_id',
        'full_name',
        'alamat',
        'telp',
        'prodi_id',
        'status_magang',
        'profile_picture',

        // <<-- tambahkan preferensi di sini
        'pref',           // preferensi kerja
        'skill',          // keahlian
        'lokasi',         // lokasi favorit
        'gaji_minimum',   // range salary (minimum)
        'durasi',         // periode magang (bulan)
    ];

    protected $casts = [
        'mhs_nim'       => 'string',
        'user_id'       => 'integer',
        'lokasi'        => 'string',
        'gaji_minimum'  => 'integer',
        'durasi'        => 'integer',
        'status_magang' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function level() {
        return $this->hasOneThrough(LevelModel::class, UserModel::class, 'user_id', 'level_id', 'user_id', 'level_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }

    
    public function lowongan()
    {
        return $this->belongsToMany(LowonganModel::class, 'm_lamaran', 'mhs_nim', 'lowongan_id');
    }

    public function feedback()
    {
        return $this->hasMany(FeedbackModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function lamaran()
    {
        return $this->hasMany(LamaranMagangModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }
}
