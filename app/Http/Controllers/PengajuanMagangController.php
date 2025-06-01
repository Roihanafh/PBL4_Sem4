<?php

namespace App\Http\Controllers;

use App\Models\DosenModel;
use App\Models\LamaranMagangModel;
use App\Models\MahasiswaModel;
use App\Models\PerusahaanModel;
use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PengajuanMagangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Pengajuan Magang',
            'list'  => ['Home', 'Pengajuan Magang']
        ];

        $page = (object) [
            'title' => 'Data Pengajuan Magang'
        ];

        $activeMenu = 'pengajuan_magang'; // set menu yang sedang aktif

        return view('pengajuan_magang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')->get();
        
        return DataTables::of($lamaran)
            ->addIndexColumn()
            ->addColumn('mahasiswa_nama', function ($lmr) {
                return $lmr->mahasiswa ? $lmr->mahasiswa->full_name : '-';
            })
            ->addColumn('mhs_nim', function ($lmr) {
                return $lmr->mahasiswa ? $lmr->mahasiswa->mhs_nim : '-';
            })
            ->addColumn('dosen_nama', function ($lmr) {
                return $lmr->dosen ? $lmr->dosen->nama : '-';
            })
            ->addColumn('tanggal_lamaran', function ($lmr) {
                return $lmr->tanggal_lamaran ?? '-';
            })
            ->addColumn('status', function ($lmr) {
                return $lmr->status ?? '-';
            })
            ->addColumn('aksi', function ($lmr) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/pengajuan-magang/' . $lmr->lamaran_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                $btn .= '<i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/pengajuan-magang/' . $lmr->lamaran_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                $btn .= '<i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/pengajuan-magang/' . $lmr->lamaran_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                $btn .= '<i class="fas fa-trash-alt"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data prodi tersebut tidak ditemukan.'
            ], 404);
        }  
        if (!$perusahaan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perusahaan tersebut tidak ditemukan.'
            ], 404);
        } 
        return view('pengajuan_magang.show_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
            'dosens' => $dosens
            
        ]);
    }

    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'dosen_id' => 'required_if:status,diterima|nullable|exists:m_dosen,dosen_id'
        ], [
            'dosen_id.required_if' => 'Dosen pembimbing wajib dipilih jika lamaran diterima.'
        ]);

        $lamaran = LamaranMagangModel::findOrFail($id);
        $lamaran->status = $request->status;
        $lamaran->dosen_id = $request->status === 'diterima' ? $request->dosen_id : null;
        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
        }
        $lamaran->save();

        return response()->json([
            'status' => true,
            'message' => 'Status lamaran berhasil diperbarui.'
        ]);
    }

    public function edit_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data prodi tersebut tidak ditemukan.'
            ], 404);
        }  
        if (!$perusahaan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perusahaan tersebut tidak ditemukan.'
            ], 404);
        } 
        return view('pengajuan_magang.edit_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
            'dosens' => $dosens
            
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak,pending,selesai',
            'dosen_id' => 'required_if:status,diterima|nullable|exists:m_dosen,dosen_id'
        ], [
            'status.in' => 'Status harus berupa diterima, ditolak, pending, atau selesai.',
            'dosen_id.required_if' => 'Dosen pembimbing wajib dipilih jika lamaran diterima.'
        ]);

        $lamaran = LamaranMagangModel::findOrFail($id);
        $lamaran->status = $request->status;
        $lamaran->dosen_id = $request->status === 'diterima' || $request->status === 'selesai' ? $request->dosen_id : null;
        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
        }else if($request->status === 'pending' || $request->status === 'ditolak'){
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Belum Magang"]);
        }
        $lamaran->save();

        return response()->json([
            'status' => true,
            'message' => 'Lamaran berhasil diperbarui.'
        ]);
    }
    public function confirm_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data prodi tersebut tidak ditemukan.'
            ], 404);
        }  
        if (!$perusahaan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perusahaan tersebut tidak ditemukan.'
            ], 404);
        } 
        return view('pengajuan_magang.confirm_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
            'dosens' => $dosens
            
        ]);
    }
    public function delete_ajax(Request $request, $lamaran_id)
    {
        try {
            $lamaran = LamaranMagangModel::where('lamaran_id', $lamaran_id)->first();

            if (!$lamaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            // Soft delete lamaran
            $lamaran->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data lamaran berhasil dihapus.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih digunakan pada data lain.'
                ], 422);
            }
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request, $lamaran_id)
    {
        try {
            $lamaran = LamaranMagangModel::withTrashed()->where('lamaran_id', $lamaran_id)->first();

            if (!$lamaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            $lamaran->restore();

            return response()->json([
                'status' => true,
                'message' => 'Data lamaran berhasil dipulihkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memulihkan data: ' . $e->getMessage()
            ], 500);
        }
    }
    // public function edit_ajax($mhs_nim)
    // {
    //     $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->find($mhs_nim);

    //     return view('mahasiswa.edit_ajax', compact('mahasiswa'));
    // }

    // public function update_ajax(Request $request, $mhs_nim)
    // {
    //     $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

    //     if (!$mahasiswa) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data mahasiswa tidak ditemukan.'
    //         ]);
    //     }

    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'username'  => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
    //         'password'  => 'nullable|min:5|max:20',
    //         'full_name' => 'required|max:100',
    //         'alamat'    => 'nullable|max:255',
    //         'telp'      => 'nullable|max:20',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validasi gagal, periksa input anda.',
    //             'msgField' => $validator->errors()
    //         ]);
    //     }

    //     try {
    //         // Update data mahasiswa
    //         $mahasiswa->full_name = $request->full_name;
    //         $mahasiswa->alamat = $request->alamat;
    //         $mahasiswa->telp = $request->telp;
    //         $mahasiswa->save();

    //         // Update user (username dan password)
    //         $user = $mahasiswa->user;
    //         $user->username = $request->username;
    //         if (!empty($request->password)) {
    //             $user->password = bcrypt($request->password);
    //         }
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data mahasiswa berhasil diperbarui.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Terjadi kesalahan saat menyimpan data.',
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }
}
