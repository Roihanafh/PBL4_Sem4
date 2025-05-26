<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaModel;
use App\Models\UserModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use App\Models\DosenModel;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    // Menampilkan halaman awal mahasiswa
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Mahasiswa',
            'list'  => ['Home', 'Mahasiswa']
        ];

        $page = (object) [
            'title' => 'Data Mahasiswa'
        ];

        $activeMenu = 'mahasiswa'; // set menu yang sedang aktif

        $prodis = ProdiModel::all(); // ambil data prodi untuk filter

        return view('mahasiswa.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'prodis' => $prodis, // kirim data prodi ke view
        ]);
    }


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $mahasiswa = MahasiswaModel::with('prodi')
                ->select('mhs_nim as nim', 'full_name as nama', 'prodi_id', 'user_id')
                ->orderBy('user_id', 'asc');

            // Filter berdasarkan prodi jika ada input filter
            if ($request->prodi_id) {
                $mahasiswa->where('prodi_id', $request->prodi_id);
            }

            return DataTables::of($mahasiswa)
                ->addIndexColumn()
                ->addColumn('prodi', function ($mhs) {
                    return $mhs->prodi ? $mhs->prodi->nama_prodi : '-';
                })
                ->addColumn('aksi', function ($mhs) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
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
        $prodis = ProdiModel::all(); // ambil semua program studi

        return view('mahasiswa.create_ajax', compact('prodis'));
    }



    public function store_ajax(Request $request)
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
        'message' => 'Data mahasiswa berhasil disimpan'
        ]);
    }

    public function confirm_ajax($nim)
    {
        $mahasiswa = MahasiswaModel::with('prodi')->where('mhs_nim', $nim)->first();

        return view('mahasiswa.confirm_ajax', compact('mahasiswa'));
    }

    public function delete_ajax(Request $request, $nim)
    {
        $mahasiswa = MahasiswaModel::where('mhs_nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Simpan ID user dulu sebelum hapus mahasiswa
        $userId = $mahasiswa->user_id;

        // Hapus mahasiswa terlebih dahulu
        $mahasiswa->delete();

        // Setelah itu baru hapus user terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data mahasiswa berhasil dihapus.'
        ]);
    }

    public function show_ajax(String $nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->where('mhs_nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.'
            ], 404);
        }

        return view('mahasiswa.show_ajax', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function edit_ajax($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->find($mhs_nim);

        return view('mahasiswa.edit_ajax', compact('mahasiswa'));
    }

    public function update_ajax(Request $request, $mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'full_name' => 'required|max:100',
            'alamat'    => 'nullable|max:255',
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
            // Update data mahasiswa
            $mahasiswa->full_name = $request->full_name;
            $mahasiswa->alamat = $request->alamat;
            $mahasiswa->telp = $request->telp;
            $mahasiswa->save();

            // Update user (username dan password)
            $user = $mahasiswa->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data mahasiswa berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }

   public function export_pdf()
    {
        $mahasiswa = DB::table('m_mahasiswa')
            ->join('m_users', 'm_mahasiswa.user_id', '=', 'm_users.user_id')
            ->join('r_auth_level', 'm_users.level_id', '=', 'r_auth_level.level_id')
            ->leftJoin('m_program_studi', 'm_mahasiswa.prodi_id', '=', 'm_program_studi.prodi_id')
            ->select(
                'm_users.username',
                'm_mahasiswa.full_name',
                'm_mahasiswa.telp',
                'm_program_studi.nama_prodi as program_studi',
                'm_mahasiswa.alamat',
                'm_mahasiswa.status_magang',
                'r_auth_level.level_name'
            )
            ->orderBy('m_mahasiswa.mhs_nim', 'asc')
            ->get();

        $pdf = Pdf::loadView('mahasiswa.export_pdf', ['mahasiswa' => $mahasiswa]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Mahasiswa ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function export_excel()
    {
        $mahasiswa = MahasiswaModel::with(['user', 'user.level', 'prodi'])
            ->orderBy('mhs_nim')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'No. Telepon');
        $sheet->setCellValue('E1', 'Program Studi');
        $sheet->setCellValue('F1', 'Alamat');
        $sheet->setCellValue('G1', 'Status Magang');
        $sheet->setCellValue('H1', 'Level');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($mahasiswa as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->user->username ?? '-');
            $sheet->setCellValue('C' . $baris, $data->full_name);
            $sheet->setCellValue('D' . $baris, $data->telp ?? '-');
            $sheet->setCellValue('E' . $baris, $data->prodi->nama_prodi ?? '-');
            $sheet->setCellValue('F' . $baris, $data->alamat ?? '-');
            $sheet->setCellValue('G' . $baris, $data->status_magang ?? '-');
            $sheet->setCellValue('H' . $baris, $data->user->level->level_name ?? '-');
            $no++;
            $baris++;
        }

        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Mahasiswa');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Mahasiswa_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function import()
    {
        return view('mahasiswa.import'); // Pastikan view-nya benar
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // Validasi file Excel
                'file_mahasiswa' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_mahasiswa');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insertedCount = 0;
            $existingUsernames = UserModel::pluck('username')->toArray();
            $existingNIMs = MahasiswaModel::pluck('mhs_nim')->toArray();

            if (count($data) > 1) {
                foreach ($data as $baris => $row) {
                    if ($baris <= 1) continue; // Skip header

                    $username = trim($row['A']);
                    $password = trim($row['B']);
                    $mhs_nim  = trim($row['C']);
                    $nama     = trim($row['D']);
                    $alamat   = trim($row['E'] ?? '');
                    $telp     = trim($row['F'] ?? '');
                    $prodi_id = trim($row['G']);

                    if (!$username || !$password || !$mhs_nim || !$nama || !$prodi_id) continue;
                    if (in_array($username, $existingUsernames) || in_array($mhs_nim, $existingNIMs)) continue;

                    $user = UserModel::create([
                        'username' => $username,
                        'password' => bcrypt($password),
                        'level_id' => 3,
                        'created_at' => now()
                    ]);

                    MahasiswaModel::create([
                        'user_id' => $user->user_id,
                        'mhs_nim' => $mhs_nim,
                        'full_name' => $nama,
                        'alamat' => $alamat ?: null,
                        'telp' => $telp ?: null,
                        'prodi_id' => $prodi_id,
                        'status_magang' => 'belum magang',
                        'created_at' => now()
                    ]);

                    $insertedCount++;
                }

                return response()->json([
                    'status' => true,
                    'message' => "$insertedCount mahasiswa berhasil diimport"
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang diimport'
            ]);
        }

        return redirect('/');
    }

    public function show_mhs($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['user', 'prodi'])->where('mhs_nim', $mhs_nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa dengan NIM ' . $mhs_nim . ' tidak ditemukan.'
            ], 404);
        }

        return view('mahasiswa.show_mhs', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function edit_mhs($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->find($mhs_nim);

        return view('mahasiswa.edit_mhs', compact('mahasiswa'));
    }

    public function update_mhs(Request $request, $mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'full_name' => 'required|max:100',
            'alamat'    => 'nullable|max:255',
            'telp'      => 'nullable|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            $mahasiswa->full_name = $request->full_name;
            $mahasiswa->alamat = $request->alamat;
            $mahasiswa->telp = $request->telp;

            if ($request->hasFile('profile_picture')) {
                if ($mahasiswa->profile_picture && Storage::disk('public')->exists($mahasiswa->profile_picture)) {
                    Storage::disk('public')->delete($mahasiswa->profile_picture);
                }

                $path = $request->file('profile_picture')->store('profile_mahasiswa', 'public');
                $mahasiswa->profile_picture = $path;
            }

            $mahasiswa->save();

            $user = $mahasiswa->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data mahasiswa berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function hapus_foto_profile($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        try {
            if ($mahasiswa->profile_picture && Storage::disk('public')->exists($mahasiswa->profile_picture)) {
                Storage::disk('public')->delete($mahasiswa->profile_picture);
            }

            $mahasiswa->profile_picture = null;
            $mahasiswa->save();

            return response()->json([
                'status' => true,
                'message' => 'Foto profil mahasiswa berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto profil.',
                'error' => $e->getMessage()
            ]);
        }
    }
}

