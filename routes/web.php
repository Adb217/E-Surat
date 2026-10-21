<?php

use App\Http\Controllers\{ArsipController, AuthController, DashboardController, KlasifikasiController,
    PegawaiController, PengaturanController, SuratKeluarController, SuratMasukController, VerifikasiController};
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// verifikasi publik (kode pendek)
Route::get('/v', [VerifikasiController::class, 'form'])->name('verifikasi.form');
Route::post('/v', [VerifikasiController::class, 'cari'])->name('verifikasi.cari');
Route::get('/v/{kode}', [VerifikasiController::class, 'show'])->name('verifikasi');
Route::post('/v/{kode}', [VerifikasiController::class, 'cek'])->name('verifikasi.cek');
Route::get('/verifikasi/{kode}', fn (string $kode) => redirect()->route('verifikasi', $kode)); // QR lama

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // arsip (keluar + masuk)
    Route::get('/surat', [ArsipController::class, 'index'])->name('surat.index');
    Route::redirect('/surat/buat', '/surat/create');

    // surat keluar
    Route::get('/surat/{surat}/pdf', [SuratKeluarController::class, 'pdf'])->name('surat.pdf');
    Route::post('/surat/{surat}/finalisasi', [SuratKeluarController::class, 'finalisasi'])->name('surat.finalisasi');
    Route::post('/surat/{surat}/batal', [SuratKeluarController::class, 'batal'])->name('surat.batal');
    Route::resource('surat', SuratKeluarController::class)->except('index');

    // surat masuk
    Route::get('/masuk/{masuk}/berkas', [SuratMasukController::class, 'berkas'])->name('masuk.berkas');
    Route::resource('masuk', SuratMasukController::class)->except('index');

    Route::resource('klasifikasi', KlasifikasiController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('pegawai', PegawaiController::class)->except(['create', 'show']);

    Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    Route::view('/bantuan', 'bantuan')->name('bantuan');
});