<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Master\AlatTangkapController;
use App\Http\Controllers\Master\JenisIkanController;
use App\Http\Controllers\Master\KapalController;
use App\Http\Controllers\Master\NelayanController;
use App\Http\Controllers\Master\TpiController;
use App\Http\Controllers\PendaratanController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Guest routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth', 'role'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account management is restricted to Pimpinan.
    Route::resource('akun', AccountController::class)->except('destroy')->middleware('role:pimpinan');

    // Master Data Resource Routes
    Route::resource('tpi', TpiController::class);
    Route::resource('nelayan', NelayanController::class);
    Route::resource('kapal', KapalController::class);
    Route::resource('alat-tangkap', AlatTangkapController::class);
    Route::resource('jenis-ikan', JenisIkanController::class);

    // Modul Operasional Pendaratan Ikan
    Route::resource('pendaratan', PendaratanController::class);
    Route::post('pendaratan/{pendaratan}/ajukan', [PendaratanController::class, 'ajukan'])->name('pendaratan.ajukan');

    // Modul Verifikasi is restricted to Verifikator.
    Route::middleware('role:verifikator')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/{pendaratan}', [VerifikasiController::class, 'show'])->name('show');
        Route::post('/{pendaratan}/approve', [VerifikasiController::class, 'approve'])->name('approve');
        Route::post('/{pendaratan}/reject', [VerifikasiController::class, 'reject'])->name('reject');
    });

    // Modul Laporan & Rekapitulasi (Role: verifikator, pimpinan, admin_tpi)
    Route::middleware('role:verifikator,pimpinan,admin_tpi')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export', [LaporanController::class, 'exportExcel'])->name('export');
        Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
    });
});
