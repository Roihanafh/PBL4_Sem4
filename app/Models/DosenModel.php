<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenModel extends Model
{
    use HasFactory;

    protected $table = 'm_dosen';
    protected $primaryKey = 'dosen_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'telp',
    ];

    public function komentarLogAktivitas()
    {
        return $this->hasMany(KomenLogAktivitasModel::class, 'pengirim_id', 'dosen_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProdiModel::class, 'dosen_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'dosen_id', 'dosen_id');
    }
}
