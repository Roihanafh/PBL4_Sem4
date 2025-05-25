<?php

// app/Http/Controllers/LowonganController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganModel;
use App\Models\PerusahaanModel;
use App\Models\PeriodeMagangModel;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LowonganController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Lowongan Magang',
            'list'  => ['Home','Lowongan']
        ];
        $page = (object)['title' => 'Manajemen Lowongan Magang'];
        $activeMenu = 'lowongan';

        return view('lowongan.index', compact('breadcrumb','page','activeMenu'));
    }

    public function list(Request $request)
    {
        if($request->ajax()) {
            $q = LowonganModel::with(['perusahaan','periode'])
                ->where('status','aktif')       // ⬅ hanya ambil yang aktif
                ->select(/* ... */);

            return DataTables::of($q)
                ->addIndexColumn()
                ->addColumn('perusahaan', fn($row)=> $row->perusahaan->nama ?? '-')
                ->addColumn('periode', fn($row)=> $row->periode->nama_periode ?? '-')
                ->addColumn('aksi', function($row){
                    $url = url("/lowongan/{$row->lowongan_id}");
                    return "
                       <div class='btn-group'>
                         <button onclick=\"modalAction('".url('/lowongan/'.$row->lowongan_id.'/show_ajax')."')\" class='btn btn-info btn-sm'><i class='fas fa-info-circle'></i></button>
                         <button onclick=\"modalAction('".url('/lowongan/'.$row->lowongan_id.'/edit_ajax')."')\" class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></button>
                         <button onclick=\"modalAction('".url('/lowongan/'.$row->lowongan_id.'/delete_ajax')."')\" class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button>
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
        return view('lowongan.create_ajax', compact('perusahaan','periode'));
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

        if($validator->fails()) {
            return response()->json([
                'status'=>false,
                'msgField'=>$validator->errors(),
                'message'=>'Validasi gagal'
            ]);
        }

        LowonganModel::create($request->all());

        return response()->json([
            'status'=>true,
            'message'=>'Lowongan berhasil ditambahkan'
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
        return response()->json(['status'=>false,'message'=>'Data tidak ditemukan'],404);
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
        $lowongan = LowonganModel::with(['perusahaan','periode'])
                      ->find($lowongan_id);
        if(!$lowongan) abort(404,'Not Found');

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
        return view('lowongan.edit_ajax', compact('lowongan','perusahaan','periode'));
    }

    public function update_ajax(Request $request, $lowongan_id)
    {
        $l = LowonganModel::find($lowongan_id);
        if(!$l) return response()->json(['status'=>false,'message'=>'Data tidak ditemukan']);

        $validator = Validator::make($request->all(), [
            // sama seperti store_ajax
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'msgField'=>$validator->errors(),
                'message'=>'Validasi gagal'
            ]);
        }

        $l->update($request->all());
        return response()->json(['status'=>true,'message'=>'Lowongan diperbarui']);
    }
}
