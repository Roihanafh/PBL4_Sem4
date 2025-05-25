<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangPenelitianModel extends Model
{
    use HasFactory;

    protected $table = 'd_bidang_penelitian';
    protected $primaryKey = 'id_minat';

    protected $fillable = [
        'bidang',
    ];

    public function dosen()
    {
        return $this->hasMany(DosenModel::class, 'id_minat', 'id_minat');
    }
}
