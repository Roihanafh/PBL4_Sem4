<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Admin',
            'list'  => ['Home', 'Admin']
        ];

        $page = (object) [
            'title' => 'Data Admin'
        ];

        $activeMenu = 'admin'; // menu yang sedang aktif

        return view('admin.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $admin = AdminModel::select('admin_id', 'nama', 'email', 'telp');

            return DataTables::of($admin)
                ->addIndexColumn()
                ->addColumn('aksi', function ($adm) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
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
        return view('admin.create_ajax'); // Pastikan view ini tersedia
    }

    public function store_ajax(Request $request)
    {
        try {
            // Validasi data untuk tabel m_users
            $validatedUser = $request->validate([
                'username' => 'required|unique:m_users,username',
                'password' => 'required|min:5',
            ]);

            // Simpan ke tabel m_users
            $user = UserModel::create([
                'username' => $validatedUser['username'],
                'password' => bcrypt($validatedUser['password']),
                'level_id' => 1, // level 1 untuk admin
            ]);

            // Validasi data untuk tabel m_admin
            $validatedAdmin = $request->validate([
                'nama' => 'required|max:100',
                'email' => 'required|email|unique:m_admin,email',
                'telp' => 'nullable|max:20',
            ]);

            // Simpan ke tabel m_admin
            AdminModel::create([
                'user_id' => $user->user_id,
                'nama' => $validatedAdmin['nama'],
                'email' => $validatedAdmin['email'],
                'telp' => $validatedAdmin['telp'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil disimpan'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'msgField' => $e->errors(),
                'message' => 'Validasi gagal, periksa inputan Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function confirm_ajax($admin_id)
    {
        $admin = AdminModel::where('admin_id', $admin_id)->first();

        return view('admin.confirm_ajax', compact('admin'));
    }

    public function delete_ajax(Request $request, $admin_id)
    {
        $admin = AdminModel::where('admin_id', $admin_id)->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ], 404);
        }

        // Simpan user_id sebelum admin dihapus
        $userId = $admin->user_id;

        // Hapus admin
        $admin->delete();

        // Hapus user terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data admin berhasil dihapus.'
        ]);
    }

    public function show_ajax($admin_id)
    {
        // Ambil data admin beserta relasi user-nya
        $admin = AdminModel::with('user')->where('admin_id', $admin_id)->first();

        // Jika data tidak ditemukan
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin dengan ID ' . $admin_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk admin
        return view('admin.show_ajax', [
            'admin' => $admin
        ]);
    }
    
    public function edit_ajax($admin_id)
    {
        // Ambil data admin beserta user-nya
        $admin = AdminModel::with('user')->find($admin_id);

        return view('admin.edit_ajax', compact('admin'));
    }

    public function update_ajax(Request $request, $admin_id)
    {
        $admin = AdminModel::with('user')->find($admin_id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $admin->user->user_id . ',user_id',
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
            // Update data admin
            $admin->nama = $request->nama;
            $admin->email = $request->email;
            $admin->telp = $request->telp;
            $admin->save();

            // Update user
            $user = $admin->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil diperbarui.'
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
