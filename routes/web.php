<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProdiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('login', [AuthController::class,'login'])->name('login');
Route::post('login', [AuthController::class,'postlogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('layouts.template');
});

Route::get('/dashboard', [WelcomeController::class, 'index']);

Route::group(['prefix' => 'mahasiswa'], function () {
    Route::get('/', [MahasiswaController::class, 'index']);
    Route::post('/list', [MahasiswaController::class, 'list']);
    Route::get('/create_ajax', [MahasiswaController::class, 'create_ajax']);
    Route::post('/ajax', [MahasiswaController::class, 'store_ajax']);
    Route::get('/{nim}/delete_ajax', [MahasiswaController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
    Route::delete('/{nim}/delete_ajax', [MahasiswaController::class, 'delete_ajax']); // Eksekusi penghapusan
    Route::get('/{nim}/show_ajax', [MahasiswaController::class, 'show_ajax']);
    Route::get('/{nim}/edit_ajax', [MahasiswaController::class, 'edit_ajax']);
    Route::put('/{nim}/update_ajax', [MahasiswaController::class, 'update_ajax']);
    Route::get('/export_pdf', [MahasiswaController::class, 'export_pdf']);
    Route::get('/export_excel', [MahasiswaController::class, 'export_excel']);
    Route::post('/import_ajax', [MahasiswaController::class, 'import_ajax']);

});


Route::group(['prefix' => 'dosen'], function () {
    Route::get('/', [DosenController::class, 'index']);
    Route::post('/list', [DosenController::class, 'list']);
    Route::get('/create_ajax', [DosenController::class, 'create_ajax']);
    Route::post('/ajax', [DosenController::class, 'store_ajax']);
    Route::get('/{dosen_id}/delete_ajax', [DosenController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
    Route::delete('/{dosen_id}/delete_ajax', [DosenController::class, 'delete_ajax']);
    Route::get('/{dosen_id}/show_ajax', [DosenController::class, 'show_ajax']);
    Route::get('/{dosen_id}/edit_ajax', [DosenController::class, 'edit_ajax']);
    Route::put('/{dosen_id}/update_ajax', [DosenController::class, 'update_ajax']);
    Route::get('/export_pdf', [DosenController::class, 'export_pdf']);
    Route::get('/export_excel', [DosenController::class, 'export_excel']);
    Route::post('/import_ajax', [DosenController::class, 'import_ajax']);

});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::post('/list', [AdminController::class, 'list']);
    Route::get('/create_ajax', [AdminController::class, 'create_ajax']);
    Route::post('/ajax', [AdminController::class, 'store_ajax']);
    Route::get('/{admin_id}/delete_ajax', [AdminController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
    Route::delete('/{admin_id}/delete_ajax', [AdminController::class, 'delete_ajax']);
    Route::get('/{admin_id}/show_ajax', [AdminController::class, 'show_ajax']);
    Route::get('/{admin_id}/edit_ajax', [AdminController::class, 'edit_ajax']);
    Route::put('/{admin_id}/update_ajax', [AdminController::class, 'update_ajax']);
    Route::get('/export_pdf', [AdminController::class, 'export_pdf']);
    Route::get('/export_excel', [AdminController::class, 'export_excel']);
    Route::post('/import_ajax', [AdminController::class, 'import_ajax']);
    

});

Route::group(['prefix' => 'periode'], function () {
    Route::get('/', [PeriodeController::class, 'index']);
    Route::post('/list', [PeriodeController::class, 'list']);
    Route::get('/create_ajax', [PeriodeController::class, 'create_ajax']);
    Route::post('/ajax', [PeriodeController::class, 'store_ajax']);
    Route::get('/{periode_id}/delete_ajax', [PeriodeController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
    Route::delete('/{periode_id}/delete_ajax', [PeriodeController::class, 'delete_ajax']); // Eksekusi penghapusan
    Route::get('/{periode_id}/show_ajax', [PeriodeController::class, 'show_ajax']);
    Route::get('/{periode_id}/edit_ajax', [PeriodeController::class, 'edit_ajax']);
    Route::put('/{periode_id}/update_ajax', [PeriodeController::class, 'update_ajax']);
    Route::get('/export_pdf', [PeriodeController::class, 'export_pdf']);
    Route::get('/export_excel', [PeriodeController::class, 'export_excel']);
    Route::post('/import_ajax', [PeriodeController::class, 'import_ajax']);
});

Route::group(['prefix' => 'prodi'], function () {
    Route::get('/', [ProdiController::class, 'index']);
    Route::post('/list', [ProdiController::class, 'list']);
    Route::get('/create_ajax', [ProdiController::class, 'create_ajax']);
    Route::post('/ajax', [ProdiController::class, 'store_ajax']);
    Route::get('/{prodi_id}/delete_ajax', [ProdiController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
    Route::delete('/{prodi_id}/delete_ajax', [ProdiController::class, 'delete_ajax']); // Eksekusi penghapusan
    Route::get('/{prodi_id}/show_ajax', [ProdiController::class, 'show_ajax']);
    Route::get('/{prodi_id}/edit_ajax', [ProdiController::class, 'edit_ajax']);
    Route::put('/{prodi_id}/update_ajax', [ProdiController::class, 'update_ajax']);
    Route::get('import', [ProdiController::class, 'import']);
    Route::post('import_ajax', [ProdiController::class, 'import_ajax']);
    Route::get('export_excel', [ProdiController::class, 'export_excel']);
    Route::get('export_pdf', [ProdiController::class, 'export_pdf']);
    
});