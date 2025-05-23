<?php 
 
namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\DosenModel;
use App\Models\UserModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller 
{ 
    public function login() 
    { 
        if(Auth::check()){ // jika sudah login, maka redirect ke halaman home 
            return redirect('/'); 
        } 
        return view('auth.login'); 
    } 
 
    public function postlogin(Request $request) 
    { 
        if ($request->ajax() || $request->wantsJson()) { 
            $credentials = $request->only('username', 'password'); 

            if (Auth::attempt($credentials)) { 
                $user = Auth::user();

                // Redirect berdasarkan level
                switch ($user->level->level_name) {
                    case 'admin':
                        $redirectUrl = url('/dashboard-admin');
                        break;
                    case 'dosen':
                        $redirectUrl = url('/dashboard-dosen');
                        break;
                    case 'mahasiswa':
                        $redirectUrl = url('/dashboard-mahasiswa');
                        break;
                    default:
                        $redirectUrl = url('/dashboard');
                }

                return response()->json([ 
                    'status' => true, 
                    'message' => 'Login Berhasil', 
                    'redirect' => $redirectUrl 
                ]); 
            }

            return response()->json([ 
                'status' => false, 
                'message' => 'Login Gagal' 
            ]); 
        }

        return redirect('login'); 
    }
 
 
    public function logout(Request $request) 
    { 
        Auth::logout(); 
 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();     
        return redirect('login'); 
    } 

    public function registerMahasiswa()
    {
        $prodis = ProdiModel::all(); // ambil semua data program studi
        return view('register.register_mhs', compact('prodis'));
    }

    public function storeMahasiswa(Request $request)
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
        'message' => 'Registrasi Anda Berhasil'
        ]);
    }

    public function registerDosen()
    {
        return view('register.register_dsn');
    }

    public function storeDosen(Request $request)
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
            'message' => 'Registrasi Anda Berhasil'
        ]);
    }

} 