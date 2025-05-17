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
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/detail_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
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
}