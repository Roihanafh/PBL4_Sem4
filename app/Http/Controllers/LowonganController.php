<?php

// app/Http/Controllers/LowonganController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganModel;
use App\Models\PerusahaanModel;
use App\Models\PeriodeMagangModel;
use Illuminate\Support\Facades\Auth;
use App\Models\MahasiswaModel;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Services\SmartRecommendationService;


class LowonganController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Lowongan Magang',
            'list'  => ['Home', 'Lowongan']
        ];
        $page = (object)['title' => 'Manajemen Lowongan Magang'];
        $activeMenu = 'lowongan';

        return view('lowongan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $q = LowonganModel::with(['perusahaan', 'periode'])
                ->where('status', 'aktif')       // ⬅ hanya ambil yang aktif
                ->select(/* ... */);

            return DataTables::of($q)
                ->addIndexColumn()
                ->addColumn('perusahaan', fn($row) => $row->perusahaan->nama ?? '-')
                ->addColumn('periode', fn($row) => $row->periode->periode ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = url("/lowongan/{$row->lowongan_id}");
                    return "
                       <div class='btn-group'>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/show_ajax') . "')\" class='btn btn-info btn-sm'><i class='fas fa-info-circle'></i></button>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/edit_ajax') . "')\" class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></button>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/delete_ajax') . "')\" class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button>
                       </div>";
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create_ajax()
    {
        $perusahaan = PerusahaanModel::all();
        $periode    = PeriodeMagangModel::all();
        return view('lowongan.create_ajax', compact('perusahaan', 'periode'));
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul'                => 'required|max:255',
            'deskripsi'            => 'required',
            'tanggal_mulai_magang' => 'required|date',
            'deadline_lowongan'    => 'required|date|after_or_equal:tanggal_mulai_magang',
            'lokasi'               => 'required',
            'perusahaan_id'        => 'required|exists:tbl_perusahaan,perusahaan_id',
            'periode_id'           => 'required|exists:tbl_periode,periode_id',
            'sylabus_path'         => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors(),
                'message' => 'Validasi gagal'
            ]);
        }

        LowonganModel::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Lowongan berhasil ditambahkan'
        ]);
    }

    public function confirm_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::find($lowongan_id);
        return view('lowongan.confirm_ajax', compact('lowongan'));
    }

    public function delete_ajax(Request $request, $lowongan_id)
    {
        $l = LowonganModel::find($lowongan_id);
        if (!$l) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // bukan delete(), tapi update status
        $l->update(['status' => 'nonaktif']);

        return response()->json([
            'status'  => true,
            'message' => 'Lowongan berhasil dinonaktifkan'
        ]);
    }

    public function show_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::with(['perusahaan', 'periode'])
            ->find($lowongan_id);
        if (!$lowongan) abort(404, 'Not Found');

        // convert string ke Carbon
        $lowongan->tanggal_mulai_magang = Carbon::parse($lowongan->tanggal_mulai_magang);
        $lowongan->deadline_lowongan    = Carbon::parse($lowongan->deadline_lowongan);
        return view('lowongan.show_ajax', compact('lowongan'));
    }

    public function edit_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::find($lowongan_id);
        $perusahaan = PerusahaanModel::all();
        $periode    = PeriodeMagangModel::all();
        return view('lowongan.edit_ajax', compact('lowongan', 'perusahaan', 'periode'));
    }

    public function update_ajax(Request $request, $lowongan_id)
    {
        $l = LowonganModel::find($lowongan_id);
        if (!$l) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);

        $validator = Validator::make($request->all(), [
            // sama seperti store_ajax
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors(),
                'message' => 'Validasi gagal'
            ]);
        }

        $l->update($request->all());
        return response()->json(['status' => true, 'message' => 'Lowongan diperbarui']);
    }

    public function rekomendasi(Request $request, SmartRecommendationService $smart)
    {
        // ---------------------------
        // 1. Ambil data mahasiswa
        // ---------------------------
        $mhs = MahasiswaModel::where('user_id', Auth::id())->firstOrFail();

        // Pecah keyword pref & skill
        $prefKeywords  = array_filter(array_map('trim', explode(',', $mhs->pref)));
        $skillKeywords = array_filter(array_map('trim', explode(',', $mhs->skill)));
        $totalPref     = count($prefKeywords) ?: 1;
        $totalSkill    = count($skillKeywords) ?: 1;

        // Gaji minimum & durasi preferensi mahasiswa
        $gajiMinimumMahasiswa = (float) $mhs->gaji_minimum;
        $durasiPreferensiMhs  = (float) $mhs->durasi; // misal 3 atau 6

        // ---------------------------
        // 2. Query lowongan aktif + gaji >= minimum
        // ---------------------------
        $q = LowonganModel::with(['perusahaan', 'periode'])
            ->where('status', 'aktif')
            ->where('gaji', '>=', $gajiMinimumMahasiswa);

        // 3. Filter tambahan (posisi, skill, lokasi, gaji, durasi)
        if ($request->filled('posisi')) {
            $q->where('judul', 'like', '%' . $request->posisi . '%');
        }
        if ($request->filled('skill')) {
            $q->where('deskripsi', 'like', '%' . $request->skill . '%');
        }
        if ($request->filled('lokasi')) {
            $q->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }
        if ($request->filled('gaji')) {
            $q->where('gaji', '>=', $request->gaji);
        }
        if ($request->filled('durasi')) {
            $q->whereHas('periode', fn($sub) =>
                $sub->where('durasi', $request->durasi)
            );
        }

        // 4. Eksekusi query
        $lowongan = $q->get();

        // Jika kosong, kita tetap kirimkan $lowongan kosong
        // (partial akan menampilkan pesan “Tidak ada lowongan”)
        // 5. Peta ke array untuk SMART
        if (!$lowongan->isEmpty()) {
            $raw = $lowongan->map(function ($l) use (
                $mhs,
                $prefKeywords,
                $skillKeywords,
                $totalPref,
                $totalSkill,
                $durasiPreferensiMhs
            ) {
                // (…hitungan SMART sama seperti before…)
                $prefMatches = 0;
                foreach ($prefKeywords as $kw) {
                    if ($kw !== '' && (
                        stripos($l->judul, $kw) !== false ||
                        stripos($l->deskripsi, $kw) !== false
                    )) {
                        $prefMatches++;
                    }
                }
                $prefValue = $prefMatches / $totalPref;

                $skillMatches = 0;
                foreach ($skillKeywords as $kw) {
                    if ($kw !== '' && stripos($l->deskripsi, $kw) !== false) {
                        $skillMatches++;
                    }
                }
                $skillValue = $skillMatches / $totalSkill;

                $lokasiValue = 0.0;
                if (strtolower($l->lokasi) === strtolower($mhs->lokasi)) {
                    $lokasiValue = 1.0;
                } elseif (
                    substr(strtolower($l->lokasi), 0, 2) === substr(strtolower($mhs->lokasi), 0, 2)
                ) {
                    $lokasiValue = 0.5;
                }

                $durasiLowongan = (float) $l->periode->durasi;
                $minDurasi       = 3.0;
                $maxDurasi       = 6.0;
                $rangeDurasi     = $maxDurasi - $minDurasi;
                $diff            = abs($durasiLowongan - $durasiPreferensiMhs);
                $durasiValue     = ($rangeDurasi > 0)
                    ? 1.0 - ($diff / $rangeDurasi)
                    : 1.0;
                $durasiValue = max(0.0, min(1.0, $durasiValue));

                $gajiRaw = (float) $l->gaji;

                return [
                    'id'     => $l->lowongan_id,
                    'pref'   => $prefValue,
                    'skill'  => $skillValue,
                    'lokasi' => $lokasiValue,
                    'gaji'   => $gajiRaw,
                    'durasi' => $durasiValue,
                ];
            })->toArray();

            // 6. Hitung skor SMART
            $ranking = $smart->rank($raw);

            // 7. Gabungkan dan urutkan
            $ranked = collect($ranking)
                ->map(fn($r) =>
                    $lowongan
                        ->firstWhere('lowongan_id', $r['id'])
                        ->setAttribute('smart_score', $r['score'])
                )
                ->sortByDesc('smart_score')
                ->values();
        } else {
            // kalau kosong, tetap buatlah koleksi kosong
            $ranked = collect([]);
        }

        // 8. Jika ini AJAX, render partial list saja:
        if ($request->ajax()) {
            // kita akan membuat partial: resources/views/rekomendasi/partials/list.blade.php
            $html = view('rekomendasi.partials.list', [
                'lowongan' => $ranked,
                'mhs'      => $mhs,
            ])->render();

            return response()->json(['html' => $html]);
        }

        // 9. Bukan AJAX → kembalikan view penuh:
        return view('rekomendasi.index', [
            'breadcrumb' => (object)[
                'title' => 'Rekomendasi Magang',
                'list'  => ['Dashboard Mahasiswa', 'Rekomendasi Magang'],
            ],
            'page'       => (object)['title' => 'Rekomendasi Magang'],
            'activeMenu' => 'rekomendasi',
            'lowongan'   => $ranked,
            'mhs'        => $mhs,
        ]);
    }

    public function show(Request $request, SmartRecommendationService $smart, $lowongan_id)
    {
        // 1) Ambil data lowongan utama (detail) yang akan ditampilkan
        $lowongan = LowonganModel::with(['perusahaan', 'periode', 'lamaran'])
            ->findOrFail($lowongan_id);

        // 2) Ambil statistik
        $totalJobs = LowonganModel::where('status', 'aktif')->count();
        $totalCompanies = LowonganModel::where('status', 'aktif')
            ->distinct('perusahaan_id')->count('perusahaan_id');
        $totalPositions = LowonganModel::where('status', 'aktif')->sum('kuota');

        // 3) Ambil data mahasiswa yang login
        $mhs = MahasiswaModel::where('user_id', Auth::id())->firstOrFail();

        // 4) Siapkan query lowongan lain untuk sidebar (sama filter-nya)
        $q = LowonganModel::with(['perusahaan', 'periode', 'lamaran'])
            ->where('status', 'aktif')
        ->when($mhs->gaji_minimum, fn ($qry, $min) => $qry->where('gaji', '>=', $min));

        if ($request->filled('posisi')) {
            $q->where('judul', 'like', '%' . $request->posisi . '%');
        }
        if ($request->filled('skill')) {
            $q->where('deskripsi', 'like', '%' . $request->skill . '%');
        }
        if ($request->filled('lokasi')) {
            $q->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }
        if ($request->filled('gaji')) {
            $q->where('gaji', '>=', $request->gaji);
        }
        if ($request->filled('durasi')) {
            $q->whereHas('periode', fn($sub) =>
                $sub->where('durasi', $request->durasi)
            );
        }

        // 5) Eksekusi → daftar lowongan lain
        $lowonganList = $q->get();

        if ($lowonganList->isEmpty()) {
            // tidak ada lowongan lain, kirimkan partial kosong
            $lowonganListSorted = collect([]);
        } else {
            // Hitung SMART sama seperti sebelumnya
            $prefKeywords  = array_filter(array_map('trim', explode(',', $mhs->pref)));
            $skillKeywords = array_filter(array_map('trim', explode(',', $mhs->skill)));
            $totalPref     = count($prefKeywords) ?: 1;
            $totalSkill    = count($skillKeywords) ?: 1;
            $durasiPreferensiMhs  = (float) $mhs->durasi;

            $raw = $lowonganList->map(function ($l) use (
                $mhs,
                $prefKeywords,
                $skillKeywords,
                $totalPref,
                $totalSkill,
                $durasiPreferensiMhs
            ) {
                $prefMatches = 0;
                foreach ($prefKeywords as $kw) {
                    if ($kw !== '' && (
                        stripos($l->judul, $kw) !== false ||
                        stripos($l->deskripsi, $kw) !== false
                    )) {
                        $prefMatches++;
                    }
                }
                $prefValue = $prefMatches / $totalPref;

                $skillMatches = 0;
                foreach ($skillKeywords as $kw) {
                    if ($kw !== '' && stripos($l->deskripsi, $kw) !== false) {
                        $skillMatches++;
                    }
                }
                $skillValue = $skillMatches / $totalSkill;

                $lokasiValue = 0.0;
                if (strtolower($l->lokasi) === strtolower($mhs->lokasi)) {
                    $lokasiValue = 1.0;
                } elseif (
                    substr(strtolower($l->lokasi), 0, 2) === substr(strtolower($mhs->lokasi), 0, 2)
                ) {
                    $lokasiValue = 0.5;
                }

                $durasiLowongan = (float) $l->periode->durasi;
                $minDurasi = 3.0;
                $maxDurasi = 6.0;
                $rangeDurasi = $maxDurasi - $minDurasi;
                $diff = abs($durasiLowongan - $durasiPreferensiMhs);
                $durasiValue = ($rangeDurasi > 0)
                    ? 1.0 - ($diff / $rangeDurasi)
                    : 1.0;
                $durasiValue = max(0.0, min(1.0, $durasiValue));

                $gajiRaw = (float) $l->gaji;

                return [
                    'id'     => $l->lowongan_id,
                    'pref'   => $prefValue,
                    'skill'  => $skillValue,
                    'lokasi' => $lokasiValue,
                    'gaji'   => $gajiRaw,
                    'durasi' => $durasiValue,
                ];
            })->toArray();

            $ranking = $smart->rank($raw);

            $lowonganListSorted = collect($ranking)
                ->map(fn($r) =>
                    $lowonganList
                        ->firstWhere('lowongan_id', $r['id'])
                        ->setAttribute('smart_score', $r['score'])
                )
                ->sortByDesc('smart_score')
                ->values();
        }

    if ($request->query('ajax') === '1') {
        // render the full “show” HTML into a string
        $html = view('rekomendasi.show', [
            'lowongan'       => $lowongan,
            'lowonganList'   => $lowonganListSorted,
            'totalJobs'      => $totalJobs,
            'totalCompanies' => $totalCompanies,
            'totalPositions' => $totalPositions,
            'breadcrumb'     => (object)[
                'title' => 'Detail Lowongan',
                'list'  => ['Dashboard Mahasiswa','Rekomendasi Magang','Detail']
            ],
            'page'       => (object)['title' => 'Detail Lowongan'],
            'activeMenu' => 'rekomendasi',
            'mhs'        => $mhs,
        ])->render();

        return response()->json(['html' => $html]);
    }

    // Non-AJAX (or no ?ajax=1 in URL): render the normal Blade layout
    return view('rekomendasi.show', [
        'lowongan'       => $lowongan,
        'lowonganList'   => $lowonganListSorted,
        'totalJobs'      => $totalJobs,
        'totalCompanies' => $totalCompanies,
        'totalPositions' => $totalPositions,
        'breadcrumb'     => (object)[
            'title' => 'Detail Lowongan',
            'list'  => ['Dashboard Mahasiswa','Rekomendasi Magang','Detail']
        ],
        'page'       => (object)['title' => 'Detail Lowongan'],
        'activeMenu' => 'rekomendasi',
        'mhs'        => $mhs,
    ]);
    }
}
