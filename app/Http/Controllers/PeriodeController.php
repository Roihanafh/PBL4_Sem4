<?php

namespace App\Http\Controllers;

use App\Models\PeriodeMagangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
class PeriodeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Periode',
            'list'  => ['Home', 'Periode']
        ];

        $page = (object) [
            'title' => 'Data Periode'
        ];

        $activeMenu = 'periode'; // set menu yang sedang aktif

        return view('periode.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        $periode = PeriodeMagangModel::select('periode_id', 'periode', 'keterangan')->get();
        return DataTables::of($periode)
            ->addIndexColumn()
            ->addColumn('aksi', function ($prd) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                $btn .= '<i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                $btn .= '<i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                $btn .= '<i class="fas fa-trash-alt"></i></button>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('periode.create_ajax');
    }



    public function store_ajax(Request $request)
    {
        $validatedPeriode = $request->validate([
            'periode' => 'required',
            'keterangan' => 'required',
        ]);

        PeriodeMagangModel::create([
            'periode' => $validatedPeriode['periode'],
            'keterangan' => $validatedPeriode['keterangan'],
        ]);

       return response()->json([
        'status' => true,
        'message' => 'Data periode berhasil disimpan'
        ]);
    }

    public function confirm_ajax($periode_id)
    {
        $periode = PeriodeMagangModel::select()->where('periode_id', $periode_id)->first();

        return view('periode.confirm_ajax', compact('periode'));
    }

    public function delete_ajax(Request $request, $periode_id)
    {
        try {
            $periode = PeriodeMagangModel::where('periode_id', $periode_id)->first();

            if (!$periode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data periode tidak ditemukan.'
                ], 404);
            }

            // Hapus periode
            $periode->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data periode berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax(String $periode_id)
    {
        $periode = PeriodeMagangModel::select('periode_id', 'periode', 'keterangan')->where('periode_id', $periode_id)->first();

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data periode dengan id ' . $periode_id . ' tidak ditemukan.'
            ], 404);
        }

        return view('periode.show_ajax', [
            'periode' => $periode
        ]);
    }

    public function edit_ajax($periode_id)
    {
        $periode = PeriodeMagangModel::select()->where('periode_id', $periode_id)->first();

        return view('periode.edit_ajax', compact('periode'));
    }

    public function update_ajax(Request $request, $periode_id)
    {
        $periode = PeriodeMagangModel::select()->find($periode_id);

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'periode'  => 'required|max:20',
            'keterangan' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update data periode
            $periode->periode = $request->periode;
            $periode->keterangan = $request->keterangan;
            $periode->save();

            return response()->json([
                'status' => true,
                'message' => 'Data periode berhasil diperbarui.'
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