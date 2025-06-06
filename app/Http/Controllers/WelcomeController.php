<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'list'  => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        return view('welcome_admin', compact('breadcrumb', 'activeMenu'));
    }

    public function index_dosen()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';
        $totalMhs = MahasiswaModel::count();
        $totalMhsDiterima = LamaranMagangModel::where('status', 'diterima')
            ->distinct('mhs_nim')
            ->count('mhs_nim');
        $totalPerusahaan = PerusahaanModel::count();
        $totalLowongan   = LowonganModel::count();

        return view(
            'welcome_dosen',
            compact(
                'breadcrumb',
                'activeMenu',
                'totalMhs',
                'totalMhsDiterima',
                'totalPerusahaan',
                'totalLowongan'
            )
        );
    }

    public function index_mahasiswa()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];
        $activeMenu = 'dashboard';

        // 1. Cari data Mahasiswa yang sedang login
        $mhs = MahasiswaModel::where('user_id', Auth::id())->firstOrFail();

        // 2. Total Recommendations = semua lowongan dengan status "aktif"
        $totalRecommendations = LowonganModel::where('status', 'aktif')->count();

        // 3. Applications In Progress = lamaran milik mahasiswa yang status-nya belum selesai
        $inProgressApplications = LamaranMagangModel::where('mhs_nim', $mhs->nim)
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();

        // 4. Upcoming Deadlines = lowongan "aktif" whose deadline_lowongan dalam 7 hari ke depan
        $today      = Carbon::now()->startOfDay();
        $inSevenDays = Carbon::now()->addDays(7)->endOfDay();

        $upcomingDeadlines = LowonganModel::where('status', 'aktif')
            ->whereBetween('deadline_lowongan', [$today, $inSevenDays])
            ->orderBy('deadline_lowongan', 'asc')
            ->limit(10)  
            ->get();

        // 5. Recent Applications = 5 lamaran apa saja (tanpa orderBy, karena tidak ada 'id' atau 'created_at')
        $recentApplications = LamaranMagangModel::with('lowongan')
            ->where('mhs_nim', $mhs->nim)
            ->limit(5)
            ->get();

        return view('welcome_mahasiswa', [
            'breadcrumb'             => $breadcrumb,
            'activeMenu'             => $activeMenu,
            'totalRecommendations'   => $totalRecommendations,
            'inProgressApplications' => $inProgressApplications,
            'upcomingDeadlines'      => $upcomingDeadlines,
            'recentApplications'     => $recentApplications,
        ]);
    }
}
