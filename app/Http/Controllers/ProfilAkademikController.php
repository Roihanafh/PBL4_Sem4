<?php

namespace App\Http\Controllers;

use App\Models\BidangKeahlianModel;
use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\PrefrensiLokasiMahasiswaModel;
use App\Models\ProvinsiModel;
use App\Models\MahasiswaModel;
use App\Models\JenisDokumenModel;
use App\Models\DokumenMahasiswaModel;   
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfilAkademikController extends Controller
{
    public function index()
    {
        $title = 'Profil Akademik';
        $breadcrumb = (object) [
            'title' => 'Profil Akademik',
            'list' => 'Home, Profil Akademik'
        ];
        
        $data = MahasiswaModel::with(['minat', 'prefrensiLokasi',])
            ->where('mhs_nim', auth()->user()->mahasiswa->mhs_nim)
            ->first();
        

        $dokumenMahasiswa = $data->getDokumenWajib()->merge($data->getDokumenTambahan());



        return view('profil.index', compact('title', 'breadcrumb', 'data', 'dokumenMahasiswa'));
    }

    public function minat()
    {
        $user = Auth::user();
        $title = 'Edit Minat';

        $listMinat = BidangKeahlianModel::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'value' => $item->nama
            ];
        })->toArray();

        $tag = [];

        if ($user->level === 'mahasiswa') {
            $tag = $user->mahasiswa->minat->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->nama
                ];
            })->toArray();
        }

        $tag = [
            'items' => $tag,
            'route' => 'profil.minat.delete'
        ];

        if (request()->ajax() && request()->has('partial')) {
            return response()->json([
                'success' => true,
                'html' => view('partials._tag_cross_delete', compact('tag'))->render()
            ]);
        }

        return view('profil.form-minat', compact('tag', 'listMinat', 'title'));
    }

    public function storeMinat(Request $request)
    {
        $validated = $request->validate([
            'minat_id' => 'required|exists:m_bidang_keahlian,id',
        ]);

        $user = Auth::user();

        if ($user->level !== 'mahasiswa') {
            abort(403);
        }

        $user->mahasiswa->minat()->syncWithoutDetaching([$validated['minat_id']]);

        return $this->partialMinatReload($user);
    }

    public function destroyMinat($id)
    {
        $user = Auth::user();

        if ($user->level !== 'mahasiswa') {
            abort(403);
        }

        $user->mahasiswa->minat()->detach($id);

        return $this->partialMinatReload($user);
    }

    public function partialMinatReload($user)
    {
        $tagItems = [];

        if ($user->level === 'mahasiswa') {
            $tagItems = $user->mahasiswa->minat->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->nama
                ];
            })->toArray();
        }

        $tag = [
            'items' => $tagItems,
            'route' => 'profil.minat.delete'
        ];

        return response()->json([
            'success' => true,
            'html' => view('partials._tag_cross_delete', compact('tag'))->render()
        ]);
    }

    public function prefrensiLokasi()
    {
        $user = Auth::user();
        $title = 'Edit Prefrensi Lokasi';

        $tagItems = [];

        if ($user->level === 'mahasiswa') {
            $tagItems = $user->mahasiswa->prefrensiLokasi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->nama_tampilan
                ];
            })->toArray();
        }

        $tag = [
            'items' => $tagItems,
            'route' => 'profil.prefrensi-lokasi.delete'
        ];

        if (request()->ajax() && request()->has('partial')) {
            return response()->json([
                'success' => true,
                'html' => view('partials._tag_cross_delete', compact('tag'))->render()
            ]);
        }

        return view('profil.form-prefrensi-lokasi', compact('tag', 'title'));
    }

    public function storePrefrensiLokasi(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 'mahasiswa') {
            abort(403);
        }

        $validated = $request->validate([
            'prefrensi_lokasi_id_type' => 'required|in:provinsi,kabupaten,kecamatan,desa',
            'prefrensi_lokasi_id' => 'required|integer',
            'prefrensi_lokasi_id_input' => 'required|string|max:255',
        ]);

        // ✅ Cek apakah lokasi sudah ada
        $type = $validated['prefrensi_lokasi_id_type'] . '_id';
        $exists = PrefrensiLokasiMahasiswaModel::where('mhs_nim', $user->mahasiswa->mhs_nim)
            ->where($type, $validated['prefrensi_lokasi_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi sudah ditambahkan.'
            ]);
        }

        $lokasi = new PrefrensiLokasiMahasiswaModel();
        $lokasi->mhs_nim = $user->mahasiswa->mhs_nim;
        $lokasi->nama_tampilan = $validated['prefrensi_lokasi_id_input'];
        $lokasi->negara_id = 1;

        switch ($validated['prefrensi_lokasi_id_type']) {
            case 'provinsi':
                $provinsi = ProvinsiModel::findOrFail($validated['prefrensi_lokasi_id']);
                $lokasi->provinsi_id = $provinsi->id;
                $lokasi->latitude = $provinsi->latitude;
                $lokasi->longitude = $provinsi->longitude;
                break;

            case 'kabupaten':
                $kab = KabupatenModel::findOrFail($validated['prefrensi_lokasi_id']);
                $lokasi->kabupaten_id = $kab->id;
                $lokasi->provinsi_id = $kab->provinsi_id;
                $lokasi->latitude = $kab->latitude;
                $lokasi->longitude = $kab->longitude;
                break;

            case 'kecamatan':
                $kec = KecamatanModel::findOrFail($validated['prefrensi_lokasi_id']);
                $lokasi->kecamatan_id = $kec->id;
                $lokasi->kabupaten_id = $kec->kabupaten_id;
                $lokasi->provinsi_id = KabupatenModel::find($kec->kabupaten_id)->provinsi_id;
                $lokasi->latitude = $kec->latitude;
                $lokasi->longitude = $kec->longitude;
                break;

            case 'desa':
                $desa = DesaModel::findOrFail($validated['prefrensi_lokasi_id']);
                $kec = KecamatanModel::findOrFail($desa->kecamatan_id);
                $lokasi->desa_id = $desa->id;
                $lokasi->kecamatan_id = $desa->kecamatan_id;
                $lokasi->kabupaten_id = $kec->kabupaten_id;
                $lokasi->provinsi_id = KabupatenModel::find($kec->kabupaten_id)->provinsi_id;
                $lokasi->latitude = $kec->latitude;
                $lokasi->longitude = $kec->longitude;
                break;
        }

        $lokasi->save();

        return $this->partialPrefrensiLokasiReload($user);
    }

    public function destroyPrefrensiLokasi($id)
    {
        $user = Auth::user();

        if ($user->level !== 'mahasiswa') {
            abort(403);
        }

        PrefrensiLokasiMahasiswaModel::where('id', $id)->where('mhs_nim', $user->mahasiswa->mhs_nim)->delete();

        return $this->partialPrefrensiLokasiReload($user);
    }

    public function partialPrefrensiLokasiReload($user)
    {
        $tagItems = [];

        if ($user->level === 'mahasiswa') {
            $tagItems = $user->mahasiswa->prefrensiLokasi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->nama_tampilan
                ];
            })->toArray();
        }

        $tag = [
            'items' => $tagItems,
            'route' => 'profil.prefrensi-lokasi.delete'
        ];

        return response()->json([
            'success' => true,
            'html' => view('partials._tag_cross_delete', compact('tag'))->render()
        ]);
    }

    public function tambahDokumen()
    {
        $title = "Tambah Dokumen";
        $dokumenTambahan = JenisDokumenModel::where('default', 0)->get();

        return view('profil.form-dokumen', compact('title', 'dokumenTambahan'));
    }

    public function storeDokumen(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
            'jenis_dokumen_id' => 'required|exists:m_jenis_dokumen,id',
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
            'label' => $request->input('label'),
            'nama' => $fileName,
            'path' => 'users/dokumen/'
        ]);

        return redirect()->route('profil.index')->with('success', 'Dokumen berhasil diunggah');    
    }

    public function partialTagReload($items, $route)
    {
        $tag = ['items' => $items, 'route' => $route];

        return response()->json([
            'success' => true,
            'html' => view('partials._tag_cross_delete', compact('tag'))->render()
        ]);
    }
}



