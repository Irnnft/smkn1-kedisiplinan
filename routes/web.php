<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\KepsekDashboardController;
use App\Http\Controllers\Dashboard\KaprodiDashboardController; // Pastikan di-import
use App\Http\Controllers\Dashboard\WaliKelasDashboardController;
use App\Http\Controllers\Dashboard\OrtuDashboardController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JenisPelanggaranController;
use App\Http\Controllers\TindakLanjutController;

// --- RUTE LOGIN ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- RUTE TERPROTEKSI ---
Route::middleware(['auth'])->group(function () {

    // 1. AREA ADMIN (Operator & Waka)
    Route::middleware(['role:Operator Sekolah,Waka Kesiswaan'])->group(function () {
        Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');
        Route::resource('siswa', SiswaController::class);
        Route::resource('users', UserController::class);
        Route::resource('jenis-pelanggaran', JenisPelanggaranController::class);
        // Halaman Data Riwayat Lengkap
        Route::get('/riwayat-pelanggaran', [\App\Http\Controllers\RiwayatController::class, 'index'])
             ->name('riwayat.index');
    });

    // 2. AREA KEPALA SEKOLAH
    Route::middleware(['role:Kepala Sekolah'])->group(function () {
        Route::get('/dashboard/kepsek', [KepsekDashboardController::class, 'index'])->name('dashboard.kepsek');
    });

    // 3. AREA KAPRODI (INI YANG TADI ERROR 404 KARENA HILANG)
    Route::middleware(['role:Kaprodi'])->group(function () {
        Route::get('/dashboard/kaprodi', [KaprodiDashboardController::class, 'index'])->name('dashboard.kaprodi');
    });


    // === AREA WALI KELAS ===
    Route::middleware(['role:Wali Kelas'])->group(function () {
        Route::get('/dashboard/walikelas', [WaliKelasDashboardController::class, 'index'])->name('dashboard.walikelas');
    });

    // === AREA BERSAMA (MANAJEMEN KASUS) ===
    // Rute ini bisa diakses oleh Wali Kelas, Waka, Kepsek, dan Operator
    Route::middleware(['role:Wali Kelas,Waka Kesiswaan,Kepala Sekolah,Operator Sekolah'])->group(function () {
        Route::get('/kasus/{id}/kelola', [TindakLanjutController::class, 'edit'])->name('kasus.edit');
        Route::put('/kasus/{id}/update', [TindakLanjutController::class, 'update'])->name('kasus.update');
        Route::get('/kasus/{id}/cetak', [TindakLanjutController::class, 'cetakSurat'])->name('kasus.cetak');
    });



    // (Opsional: Buka akses Kasus/Cetak untuk Waka & Kepsek juga jika diperlukan)
    // Anda bisa membuat route grup terpisah untuk 'kasus' yang bisa diakses banyak role

    // 5. AREA GURU (Catat Pelanggaran)
    // Waka, Wali Kelas, dan Guru boleh mencatat
    Route::middleware(['role:Guru,Wali Kelas,Waka Kesiswaan,Kaprodi'])->group(function () {
        Route::get('/pelanggaran/catat', [PelanggaranController::class, 'create'])->name('pelanggaran.create');
        Route::post('/pelanggaran/store', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
    });

    // 6. AREA ORANG TUA
    Route::middleware(['role:Orang Tua'])->group(function () {
        Route::get('/dashboard/ortu', [OrtuDashboardController::class, 'index'])->name('dashboard.ortu');
    });

    // Update Rute Area Admin/Waka agar Kepsek juga bisa akses riwayat
    Route::middleware(['role:Operator Sekolah,Waka Kesiswaan,Kepala Sekolah'])->group(function () { // <-- Tambah Kepala Sekolah
        Route::get('/riwayat-pelanggaran', [\App\Http\Controllers\RiwayatController::class, 'index'])
             ->name('riwayat.index');
    });

});