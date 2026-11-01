<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\OmzetController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PrediksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Route umum yang dapat diakses oleh semua peran (Bagian Keuangan & Pemilik Usaha)
    Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/prediksi',           [PrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/jalankan', [PrediksiController::class, 'jalankan'])->name('prediksi.jalankan');

    // Route data omzet (Read-Only dapat diakses oleh Pemilik & Keuangan)
    Route::get('/omzet',           [OmzetController::class, 'index'])->name('omzet.index');
    Route::get('/omzet/riwayat',   [OmzetController::class, 'index'])->name('omzet.riwayat');

    // Route khusus Bagian Keuangan (Aksi Mutasi Data Omzet)
    Route::middleware('keuangan')->group(function () {
        Route::get('/omzet/input',     [OmzetController::class, 'index'])->name('omzet.create');
        Route::post('/omzet',          [OmzetController::class, 'store'])->name('omzet.store');
        Route::get('/omzet/{id}/edit', [OmzetController::class, 'edit'])->name('omzet.edit');
        Route::put('/omzet/{id}',      [OmzetController::class, 'update'])->name('omzet.update');
        Route::delete('/omzet/{id}',   [OmzetController::class, 'destroy'])->name('omzet.destroy');
    });

    // Route khusus Pemilik Usaha (Pengaturan EWS & Notifikasi)
    Route::middleware('pemilik')->group(function () {
        Route::get('/pengaturan',        [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan',       [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::get('/notifikasi',        [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/unduh',  [NotifikasiController::class, 'unduhLog'])->name('notifikasi.unduh');
    });
});

