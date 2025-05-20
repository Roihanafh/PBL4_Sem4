<?php

namespace App\Http\Controllers;

use App\Models\LamaranMagangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PengajuanMagangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Pengajuan Magang',
            'list'  => ['Home', 'Pengajuan Magang']
        ];

        $page = (object) [
            'title' => 'Data Pengajuan Magang'
        ];

        $activeMenu = 'pengajuan_magang'; // set menu yang sedang aktif

        return view('pengajuan_magang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
{
    $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')->get();

    return DataTables::of($lamaran)
        ->addIndexColumn()
        ->addColumn('mahasiswa_nama', function ($lmr) {
            return $lmr->mahasiswa ? $lmr->mahasiswa->full_name : '-';
        })
        ->addColumn('mhs_nim', function ($lmr) {
            return $lmr->mahasiswa ? $lmr->mahasiswa->mhs_nim : '-';
        })
        ->addColumn('dosen_nama', function ($lmr) {
            return $lmr->dosen ? $lmr->dosen->nama : '-';
        })
        ->addColumn('tanggal_lamaran', function ($lmr) {
            return $lmr->tanggal_lamaran ?? '-';
        })
        ->addColumn('status', function ($lmr) {
            return $lmr->status ?? '-';
        })
        ->addColumn('aksi', function ($lmr) {
            $btn  = '<div class="btn-group" role="group">';
            $btn .= '<button onclick="modalAction(\''.url('/pengajuanMagang/' . $lmr->lamaran_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
            $btn .= '<i class="fas fa-info-circle"></i></button>';
            $btn .= '<button onclick="modalAction(\''.url('/pengajuanMagang/' . $lmr->lamaran_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
            $btn .= '<i class="fas fa-edit"></i></button>';
            $btn .= '<button onclick="modalAction(\''.url('/pengajuanMagang/' . $lmr->lamaran_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
            $btn .= '<i class="fas fa-trash-alt"></i></button>';
            $btn .= '</div>';
            return $btn;
        })
        ->rawColumns(['aksi'])
        ->make(true);
}

}
