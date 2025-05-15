<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackModel extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $primaryKey = 'feedback_id';
    public $timestamps = false;

    protected $fillable = [
        'mhs_nim',
        'target_type',
        'lowongan_id',
        'rating',
        'komentar',
        'created_at',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'nim');
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganModel::class, 'target_id', 'lowongan_id')
                    ->where('target_type', 'lowongan');
    }
}
