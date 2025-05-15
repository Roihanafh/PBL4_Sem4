<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';
    public $timestamps = false;

    protected $fillable = [
        'penerima_id',
        'lamaran_id',
        'judul',
        'pesan',
        'waktu_dibuat',
        'status_baca',
        'tipe',
    ];

    public function lamaran()
    {
        return $this->belongsTo(LamaranMagangModel::class, 'lamaran_id');
    }
}
