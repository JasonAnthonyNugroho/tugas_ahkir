<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PertandinganController;
use Illuminate\Support\Facades\Route;

// Jalur Publik (Mahasiswa)
Route::get('/', [PertandinganController::class, 'index'])->name('home');
Route::get('/history', [PertandinganController::class, 'history'])->name('history');
Route::get('/pertandingan/{pertandingan}', [PertandinganController::class, 'show'])->name('pertandingan.show');

// Jalur Autentikasi
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Jalur Khusus (Panitia)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [PertandinganController::class, 'adminDashboard'])->name('admin.index');
    Route::get('/admin/skor', [PertandinganController::class, 'manageScore'])->name('admin.skor');
    Route::post('/admin/store', [PertandinganController::class, 'store'])->name('pertandingan.store');
    Route::post('/admin/bracket/generate', [PertandinganController::class, 'generateBracket'])->name('admin.bracket.generate');
    Route::post('/admin/bracket/{tournament}/reroll', [PertandinganController::class, 'rerollBracket'])->name('admin.bracket.reroll');
    Route::patch('/admin/pertandingan/{pertandingan}/quick-update', [PertandinganController::class, 'quickUpdate'])->name('pertandingan.quick-update');
    Route::post('/admin/pertandingan/bulk-live', [PertandinganController::class, 'bulkLive'])->name('pertandingan.bulk-live');
    Route::patch('/pertandingan/{pertandingan}/update-score', [PertandinganController::class, 'updateScore']);
});
