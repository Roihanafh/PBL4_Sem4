<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 'm_mahasiswa';
    protected $primaryKey = 'mhs_nim';
    public $incrementing = false; // karena primary key bertipe varchar
    public $timestamps = false;

    protected $fillable = [
        'mhs_nim',
        'user_id',
        'full_name',
        'alamat',
        'telp',
        'prodi_id',
        'status_magang',
        'dosen_id',
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
        return $this->belongsTo(UserModel::class, 'prodi_id', 'prodi_id');
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
}
