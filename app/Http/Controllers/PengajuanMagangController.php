<?php

namespace App\Http\Controllers;

use App\Models\DosenModel;
use App\Models\LamaranMagangModel;
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
