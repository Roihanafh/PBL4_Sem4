<?php
// App\Services\DokumenMahasiswaService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DokumenMahasiswaService
{
    public static function upload($file)
    {
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/dokumen_mahasiswa', $fileName);

        return $fileName;
    }

    public static function delete($fileName)
    {
        if ($fileName && Storage::exists('public/dokumen_mahasiswa/' . $fileName)) {
            Storage::delete('public/dokumen_mahasiswa/' . $fileName);
        }
    }
}
