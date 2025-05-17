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

        $prodis = ProdiModel::all();
        return view('mahasiswa.index', compact('prodis'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $mahasiswa = MahasiswaModel::with('prodi')
            ->select('mhs_nim as nim', 'full_name as nama', 'prodi_id')
            ->orderBy('user_id', 'asc');

            return DataTables::of($mahasiswa)
                ->addIndexColumn()
                ->addColumn('prodi', function ($mhs) {
                    return $mhs->prodi ? $mhs->prodi->nama_prodi : '-';
                })
                ->addColumn('aksi', function ($mhs) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                    $btn .= '<i class="fas fa-trash-alt"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

   public function create_ajax()
    {
        $prodis = ProdiModel::all(); // ambil semua program studi

        return view('mahasiswa.create_ajax', compact('prodis'));
    }



    public function store_ajax(Request $request)
    {
        $validatedUser = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
        ]);

        $user = UserModel::create([
            'username' => $validatedUser['username'],
            'password' => bcrypt($validatedUser['password']),
            'level_id' => 3,
        ]);

        $validatedMhs = $request->validate([
            'mhs_nim' => 'required|unique:m_mahasiswa,mhs_nim',
            'full_name' => 'required',
            'alamat' => 'nullable',
            'telp' => 'nullable',
            'prodi_id' => 'required',
            'status_magang' => 'required',
        ]);

        MahasiswaModel::create([
            'user_id' => $user->user_id,
            'mhs_nim' => $validatedMhs['mhs_nim'],
            'full_name' => $validatedMhs['full_name'],
            'alamat' => $validatedMhs['alamat'] ?? null,
            'telp' => $validatedMhs['telp'] ?? null,
            'prodi_id' => $validatedMhs['prodi_id'],
            'status_magang' => $validatedMhs['status_magang'],
        ]);

       return response()->json([
        'status' => true,
        'message' => 'Data mahasiswa berhasil disimpan'
        ]);
    }

    public function confirm_ajax($nim)
    {
        $mahasiswa = MahasiswaModel::with('prodi')->where('mhs_nim', $nim)->first();

        return view('mahasiswa.confirm_ajax', compact('mahasiswa'));
    }

    public function delete_ajax(Request $request, $nim)
    {
        $mahasiswa = MahasiswaModel::where('mhs_nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Simpan ID user dulu sebelum hapus mahasiswa
        $userId = $mahasiswa->user_id;

        // Hapus mahasiswa terlebih dahulu
        $mahasiswa->delete();

        // Setelah itu baru hapus user terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data mahasiswa berhasil dihapus.'
        ]);
    }

    public function show_ajax(String $nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->where('mhs_nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.'
            ], 404);
        }

        return view('mahasiswa.show_ajax', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function edit_ajax($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->find($mhs_nim);

        return view('mahasiswa.edit_ajax', compact('mahasiswa'));
    }

    public function update_ajax(Request $request, $mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'full_name' => 'required|max:100',
            'alamat'    => 'nullable|max:255',
            'telp'      => 'nullable|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update data mahasiswa
            $mahasiswa->full_name = $request->full_name;
            $mahasiswa->alamat = $request->alamat;
            $mahasiswa->telp = $request->telp;
            $mahasiswa->save();

            // Update user (username dan password)
            $user = $mahasiswa->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data mahasiswa berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }

}

