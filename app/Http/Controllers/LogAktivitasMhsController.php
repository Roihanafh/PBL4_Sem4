<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktivitasMhsModel;
use App\Models\MahasiswaModel;
use App\Models\LamaranMagangModel;
use App\Models\DosenModel;
use App\Models\LowonganModel;
use App\Models\ProdiModel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class LogAktivitasMhsController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Log Aktivitas Mahasiswa',
            'list'  => ['Home', 'Log Aktivitas']
        ];

        $page = (object) [
            'title' => 'Data Log Aktivitas Mahasiswa'
        ];

        $activeMenu = 'log_aktivitas';

        $prodis = ProdiModel::all();

        return view('log_aktivitas.index', compact('breadcrumb', 'page', 'activeMenu', 'prodis'));
    }

    public function list(Request $request)
    {
            $user = Auth::user();
            $dosen = DosenModel::where('user_id', $user->user_id)->first();

            if (!$dosen) {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            $dosenId = $dosen->dosen_id;

            // Ambil daftar mhs_nim dari lamaran dosen ini
            $mahasiswa = LamaranMagangModel::where('dosen_id', $dosenId)
                ->pluck('mhs_nim')
                ->unique()
                ->values();

            if ($mahasiswa->isEmpty()) {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            $aktivitas = LogAktivitasMhsModel::with(['komentar', 'lamaran.mahasiswa.prodi'])
                ->whereHas('lamaran', function ($query) use ($mahasiswa) {
                    $query->whereIn('mhs_nim', $mahasiswa);
                });

            if ($request->filled('prodi_id')) {
                $aktivitas->whereHas('lamaran.mahasiswa', function ($query) use ($request) {
                    $query->where('prodi_id', $request->prodi_id);
                });
            }

            $aktivitas = $aktivitas->orderByDesc('waktu');

            return DataTables::of($aktivitas)
            ->addIndexColumn()
            ->addColumn('nama', fn($item) => optional($item->lamaran->mahasiswa)->full_name ?? '-')
            ->addColumn('prodi', fn($item) => optional(optional($item->lamaran->mahasiswa)->prodi)->nama_prodi ?? '-')
            ->addColumn('keterangan', fn($item) => $item->keterangan)
            ->addColumn('waktu', fn($item) => $item->waktu->format('d-m-Y H:i'))
            ->addColumn('aksi', function ($item) {
                return '<button onclick="modalAction(\'' . url('/log-aktivitas/' . $item->aktivitas_id . '/show_ajax') . '\')" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>';
        })
        ->rawColumns(['aksi'])
        ->make(true);


        return abort(404);
    }
}

