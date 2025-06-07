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
        'angkatan',
        'jenis_kelamin',
        'ipk',
        'status_magang',
        'profile_picture',

        // <<-- tambahkan preferensi di sini
        'pref',           // preferensi kerja
        'skill',          // keahlian
        'lokasi',         // lokasi favorit
        'durasi',         // periode magang (bulan)
        'tipe_bekerja', 
    ];

    protected $casts = [
        'mhs_nim'       => 'string',
        'user_id'       => 'integer',
        'lokasi'        => 'string',
        'durasi'        => 'integer',
        'status_magang' => 'string',
        'tipe_bekerja'  => 'string',
        'ipk'          => 'float',
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

    public function minat()
    {
        return $this->belongsToMany(BidangKeahlianModel::class, 't_minat_mahasiswa', 'mhs_nim', 'bidang_keahlian_id','mhs_nim', 'id');
    }

public function prefrensiLokasi()
{
    return $this->hasMany(PrefrensiLokasiMahasiswaModel::class, 'mhs_nim', 'mhs_nim');
}


    public function getGenderNameAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getAllCorPrefrensiLokasi()
    {
        return $this->prefrensiLokasi->map(function ($item) {
            return [
                'nama' => $item->nama_tampilan,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        })->toArray();
    }

    public function getDokumenWajibAttribute()
    {
        return JenisDokumenModel::where('default', 1)->get();

    }

    public function getDokumenTambahanAttribute()
    {
        return JenisDokumenModel::where('default', 0)->get();
    }

    public function getDokumenTambahan()
    {
        return DokumenMahasiswaModel::where('mhs_nim', $this->mhs_nim)
            ->whereIn('jenis_dokumen_id', $this->getDokumenTambahanAttribute()->pluck('id'))
            ->get();
    }
}