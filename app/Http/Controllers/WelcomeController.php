<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MahasiswaModel;
use App\Models\LamaranMagangModel;
use App\Models\PerusahaanModel;
use App\Models\LowonganModel;

class WelcomeController extends Controller
{
    public function index_admin()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        return view('welcome_admin', compact('breadcrumb', 'activeMenu'));
    }

    public function index_dosen()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';
        $totalMhs = MahasiswaModel::count();
        $totalMhsDiterima = LamaranMagangModel::where('status', 'diterima')->distinct('mhs_nim')->count('mhs_nim');
        $totalPerusahaan = PerusahaanModel ::count();
        $totalLowongan = LowonganModel ::count();


        return view('welcome_dosen', compact('breadcrumb', 'activeMenu', 'totalMhs', 'totalMhsDiterima', 'totalPerusahaan','totalLowongan'));
    }

    public function index_mahasiswa()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        return view('welcome_mahasiswa', compact('breadcrumb', 'activeMenu'));
    }
}
