<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DosenModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Dosen',
            'list'  => ['Home', 'Mahasiswa']
        ];

        $page = (object) [
            'title' => 'Data Mahasiswa'
        ];

        $activeMenu = 'mahasiswa'; // set menu yang sedang aktif

        return view('dosen.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
           $dosen = DosenModel::select('dosen_id', 'nama', 'email', 'telp');

            return DataTables::of($dosen)
                ->addIndexColumn()
                ->addColumn('aksi', function ($dsn) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
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
        return view('dosen.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        // Validasi data untuk tabel users
        $validatedUser = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
        ]);

        // Simpan ke tabel m_users
        $user = UserModel::create([
            'username' => $validatedUser['username'],
            'password' => bcrypt($validatedUser['password']),
            'level_id' => 2, // misal: level 2 untuk dosen
        ]);

        // Validasi data untuk tabel m_dosen
        $validatedDosen = $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:m_dosen,email',
            'telp' => 'nullable',
        ]);

        // Simpan ke tabel m_dosen
        DosenModel::create([
            'user_id' => $user->user_id,
            'nama' => $validatedDosen['nama'],
            'email' => $validatedDosen['email'],
            'telp' => $validatedDosen['telp'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data dosen berhasil disimpan'
        ]);
    }

    public function confirm_ajax($dosen_id)
    {
        $dosen = DosenModel::where('dosen_id', $dosen_id)->first();

        return view('dosen.confirm_ajax', compact('dosen'));
    }

    public function delete_ajax(Request $request, $dosen_id)
    {
        $dosen = DosenModel::where('dosen_id', $dosen_id)->first();

        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen tidak ditemukan.'
            ], 404);
        }

        // Simpan ID user dulu sebelum hapus dosen
        $userId = $dosen->user_id;

        // Hapus data dosen
        $dosen->delete();

        // Hapus user yang terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data dosen berhasil dihapus.'
        ]);
    }

    public function show_ajax($dosen_id)
    {
        // Ambil data dosen beserta relasi user-nya
        $dosen = DosenModel::with('user')->where('dosen_id', $dosen_id)->first();

        // Jika data tidak ditemukan
        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen dengan ID ' . $dosen_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk dosen
        return view('dosen.show_ajax', [
            'dosen' => $dosen
        ]);
    }

    public function edit_ajax($dosen_id)
    {
        $dosen = DosenModel::with('user')->find($dosen_id);

        return view('dosen.edit_ajax', compact('dosen'));
    }

    public function update_ajax(Request $request, $dosen_id)
    {
        $dosen = DosenModel::with('user')->find($dosen_id);

        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $dosen->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'nama'      => 'required|max:100',
            'email'     => 'required|email|max:100',
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
            // Update data dosen
            $dosen->nama = $request->nama;
            $dosen->email = $request->email;
            $dosen->telp = $request->telp;
            $dosen->save();

            // Update user
            $user = $dosen->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data dosen berhasil diperbarui.'
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