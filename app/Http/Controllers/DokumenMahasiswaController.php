<?php

namespace App\Http\Controllers;

use App\Models\DokumenMahasiswaModel;
use Illuminate\Http\Request;
use App\Services\DokumenMahasiswaService;

class DokumenMahasiswaController extends Controller
{
    public function storeDokumenMhs(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
        'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
        'label' => 'nullable|string|max:255',
    ]);


    $fileName = DokumenMahasiswaService::upload($request->file('file'));

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $path = $file->storeAs('public/users/dokumen', $fileName); // save to storage
    }

    DokumenMahasiswaModel::create([
        'mhs_nim' => auth()->user()->mahasiswa->mhs_nim,
        'jenis_dokumen_id' => $request->jenis_dokumen_id,
        'label' => $request->label,
        'nama' => $fileName,
        'path' => 'users/dokumen/' // relative to storage/app/public/
    ]);

   return $this->saveDokumen($request);
}


    public function updateDokumenMhs(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
        'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
        'label' => 'nullable|string|max:255',
        ]);

        $dokumen = DokumenMahasiswaModel::findOrFail($id);

        if ($request->hasFile('file')) {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $path = $file->storeAs('public/users/dokumen', $fileName); // save to storage
    }

        return $this->saveDokumen($request, $dokumen);
    }

    private function saveDokumen(Request $request, DokumenMahasiswaModel $dokumen = null)
{
    $request->validate([
        'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
        'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
        'label' => 'nullable|string|max:255',
    ]);

    $fileName = null;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $file->storeAs('public/users/dokumen', $fileName);
    }

    $data = [
        'mhs_nim' => auth()->user()->mahasiswa->mhs_nim,
        'jenis_dokumen_id' => $request->jenis_dokumen_id,
        'label' => $request->label,
        'nama' => $fileName,
        'path' => 'users/dokumen/',
    ];

    if ($dokumen) {
        $dokumen->update($data);
        $message = 'Dokumen berhasil diperbarui.';
    } else {
        DokumenMahasiswaModel::create($data);
        $message = 'Dokumen berhasil disimpan.';
    }

    return response()->json(['success' => $message]);
}

    public function destroyDokumenMhs($id)
    {
        $dokumen = DokumenMahasiswaModel::findOrFail($id);
        $dokumen->delete();

        return response()->json(['success' => 'Data berhasil dihapus.']);
    }

    public function downloadDokumenMhs($id)
    
    {
        
        $dokumen = DokumenMahasiswaModel::findOrFail($id);
        $filePath = $dokumen->full_path;


        if (!$dokumen->nama) {
            return response()->json(['error' => 'Dokumen tidak ditemukan.'], 404);
        }

        $filePath = storage_path('app/public/' . $dokumen->path . $dokumen->nama);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        return response()->download($filePath, $dokumen->nama);
    }
    
}
