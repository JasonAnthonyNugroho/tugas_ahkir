<?php

use App\Http\Controllers\PertandinganController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [PertandinganController::class, 'index']); // Dashboard publik
Route::get('/history', [PertandinganController::class, 'history'])->name('history');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [PertandinganController::class, 'adminDashboard'])->name('admin.index');
    Route::post('/pertandingan/store', [PertandinganController::class, 'store'])->name('pertandingan.store');
    Route::patch('/pertandingan/{pertandingan}/update-score', [PertandinganController::class, 'updateScore']);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [PertandinganController::class, 'adminDashboard'])->name('admin.index');
    Route::post('/pertandingan/store', [PertandinganController::class, 'store'])->name('pertandingan.store');
    // Tambahkan route admin lainnya di sini
});