<?php

namespace App\Http\Controllers;

use App\Models\DokumenMahasiswaModel;
use Illuminate\Http\Request;

class DokumenMahasiswaController extends Controller
{
    public function storeDokumenMhs(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
            'default' => 'required|boolean',
            'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
            'label' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $file->storeAs('public/users/dokumen', $fileName);
        }

        DokumenMahasiswaModel::create([
            'mhs_nim' => auth()->user()->mahasiswa->mhs_nim,
            'jenis_dokumen_id' => $request->input('jenis_dokumen_id'),
            'label' => $request->input('label') ? $request->input('label') : null,
            'nama' => $fileName,
            'path' => 'users/dokumen/'
        ]);


        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);

        return response()->json(['success' => 'Data berhasil disimpan.']);
    }

    public function updateDokumenMhs(Request $request, $mhs_nim)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
            'default' => 'required|boolean',
            'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
            'label' => 'nullable|string|max:255',
        ]);

        $dokumen = DokumenMahasiswaModel::findOrFail($mhs_nim);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $file->storeAs('public/users/dokumen', $fileName);
        }

        $dokumen->update([
            'mhs_nim' => auth()->user()->mahasiswa->mhs_nim,
            'jenis_dokumen_id' => $request->input('jenis_dokumen_id'),
            'label' => $request->input('label') ? $request->input('label') : null,
            'nama' => $fileName,
            'path' => 'users/dokumen/'
        ]);

        return response()->json(['success' => 'Data berhasil diupdate.']);
    }

    public function destroyDokumenMhs($mhs_nim)
    {
        $dokumen = DokumenMahasiswaModel::findOrFail($mhs_nim);
        $dokumen->delete();

        return response()->json(['success' => 'Data berhasil dihapus.']);
    }

    public function downloadDokumenMhs($mhs_nim)
    
    {
        $dokumen = DokumenMahasiswaModel::findOrFail($mhs_nim);

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
