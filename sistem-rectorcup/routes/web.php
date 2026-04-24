<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PertandinganController;
use Illuminate\Support\Facades\Route;

// Jalur Publik (Mahasiswa)
Route::get('/', [PertandinganController::class, 'index'])->name('home');
Route::get('/history', [PertandinganController::class, 'history'])->name('history');

// Jalur Autentikasi
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Jalur Khusus (Panitia)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [PertandinganController::class, 'adminDashboard'])->name('admin.index');
    Route::post('/admin/store', [PertandinganController::class, 'store'])->name('pertandingan.store');
    Route::patch('/pertandingan/{pertandingan}/update-score', [PertandinganController::class, 'updateScore']);
});
