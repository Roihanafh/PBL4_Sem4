<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomenLogAktivitasModel extends Model
{
    use HasFactory;
    protected $table = 'd_komentar_log_aktivitas';
    protected $primaryKey = 'komentar_id';
    public $timestamps = false;

    protected $fillable = [
        'aktivitas_id',
        'pengirim_id',
        'komentar',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke log aktivitas mahasiswa
     */
    public function aktivitas()
    {
        return $this->belongsTo(LogAktivitasMhsModel::class, 'aktivitas_id', 'aktivitas_id');
    }

    /**
     * Relasi ke pengirim komentar (misalnya user/mahasiswa)
     */
    public function pengirim()
    {
        return $this->belongsTo(DosenModel::class, 'pengirim_id', 'dosen_id');
    }
}
