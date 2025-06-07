<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendapatan\Http\Controllers\DashboardController;
use Modules\Pendapatan\Http\Controllers\FilterController;
use Modules\Pendapatan\Http\Controllers\PendapatanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['auth', 'permission']], function () {
    Route::prefix('pendapatan')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('pendapatan.dashboard');
        Route::get('/index', [PendapatanController::class, 'index'])->name('pendapatan.index');
        Route::get('/create', [PendapatanController::class, 'create'])->name('pendapatan.create');
        Route::post('/', [PendapatanController::class, 'store'])->name('pendapatan.store');
        Route::get('/{id}/edit', [PendapatanController::class, 'edit'])->name('pendapatan.edit');
        Route::put('/{id}', [PendapatanController::class, 'update'])->name('pendapatan.update');
        Route::delete('/{id}', [PendapatanController::class, 'destroy'])->name('pendapatan.destroy');
        Route::post('/import', [PendapatanController::class, 'importExcel'])->name('pendapatan.import');

        Route::get('/perbulan', [FilterController::class, 'perbulan'])->name('pendapatan.perbulan');
        Route::get('/pertahun', [FilterController::class, 'pertahun'])->name('pendapatan.pertahun');
        Route::get('/pegawai_perbulan', [FilterController::class, 'perbulan'])->name('pendapatan.perbulan');
        Route::get('/pegawai_pertahun', [FilterController::class, 'pertahun'])->name('pendapatan.pertahun');

        Route::get('/detail/{pegawai_id}/{bulan}/{tahun}', [PendapatanController::class, 'detail'])->name('pendapatan.detail');
        Route::get('/detail-pertahun/{pegawai_id}/{tahun}', [PendapatanController::class, 'detailPertahun'])->name('pendapatan.detail_pertahun');
        Route::get('/cetakbulan/{pegawai_id}/{bulan}/{tahun}', [PendapatanController::class, 'cetakbulan'])->name('pendapatan.cetak.bulan');
        Route::get('/cetaktahun/{pegawai_id}/{tahun}', [PendapatanController::class, 'cetaktahun'])->name('pendapatan.cetak.tahun');
    });
});
