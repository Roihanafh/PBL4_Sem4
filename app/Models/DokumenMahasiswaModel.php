<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumenMahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 't_dokumen_mahasiswa';

    protected $fillable = [
        'mhs_nim',
        'jenis_dokumen_id',
        'label',
        'nama',
        'path',
    ];

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumenModel::class, 'jenis_dokumen_id', 'mhs_nim');
    }

    public function getLabelNamaAttribute()
    {
        return $this->label ? "{$this->label} : {$this->jenisDokumen->nama}" : $this->jenisDokumen->nama;
    }

    public function getDokumenPath()
    {
        if (!$this->nama) {
            return null;
        }

        $extension = pathinfo($this->nama, PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            return asset("storage/{$this->path}{$this->nama}");
        } else if (strtolower($extension) === 'pdf') {
            return asset('images/pdf_file_icon.svg');
        } else if (in_array(strtolower($extension), ['doc', 'docx'])) {
            return asset('images/doc_file_icon.svg');
        }

        return null;
    }
}
