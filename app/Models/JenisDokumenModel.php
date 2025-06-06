<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumenModel extends Model
{
    use HasFactory;

    protected $table = 'm_jenis_dokumen';

    public function dokumen()
    {
        return $this->hasMany(DokumenMahasiswaModel::class, 'jenis_dokumen_id');
    }

    public function getDokumenPathFromMhs(int $mhsNim)
    {
        $dokumen = $this->dokumen()->where('mhs_nim', $mhsNim)->first();

        if ($dokumen) {
            $extension = pathinfo($dokumen->nama, PATHINFO_EXTENSION);

            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                return asset("storage/{$dokumen->path}{$dokumen->nama}");
            } else if (strtolower($extension) === 'pdf') {
                return asset('images/pdf_file_icon.svg');
            } else if (in_array(strtolower($extension), ['doc', 'docx'])) {
                return asset('images/doc_file_icon.svg');
            }
        }
        return null;
    }

    public function getDokumenLabelMhs($mhs_nim)
    {
        $dokumen = $this->dokumen()->where('mhs_nim', $mhs_nim)->first();

        if ($dokumen) {
            return $dokumen->label;
        }
        return null;
    }

    public function getDokumenIdMhs(int $mhsNim)
    {
        $dokumen = $this->dokumen()->where('mhs_nim', $mhsNim)->first();

        if ($dokumen) {
            return $dokumen->id;
        }
        return null;
    }
    }

