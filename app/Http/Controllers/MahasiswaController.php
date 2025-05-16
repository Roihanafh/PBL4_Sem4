<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaModel;
use App\Models\UserModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use App\Models\DosenModel;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    // Menampilkan halaman awal mahasiswa
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Mahasiswa',
            'list'  => ['Home', 'Mahasiswa']
        ];

        $page = (object) [
            'title' => 'Data Mahasiswa'
        ];

        $activeMenu = 'mahasiswa'; // set menu yang sedang aktif

        return view('mahasiswa.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $mahasiswa = MahasiswaModel::with('prodi')->select('mhs_nim as nim', 'full_name as nama', 'prodi_id');

            return DataTables::of($mahasiswa)
                ->addIndexColumn()
                ->addColumn('prodi', function ($mhs) {
                    return $mhs->prodi ? $mhs->prodi->nama_prodi : '-';
                })
                ->addColumn('aksi', function ($mhs) {
                    $btn = '<button onclick="modalAction(\'' . url('/mahasiswa/' . $mhs->nim . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $mhs->nim . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $mhs->nim . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }
}

