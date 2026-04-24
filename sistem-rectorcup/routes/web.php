<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PertandinganController;


Route::get('/', [PertandinganController::class, 'index']); // Dashboard publik

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [PertandinganController::class, 'adminDashboard'])->name('admin.index');
    Route::post('/pertandingan/store', [PertandinganController::class, 'store'])->name('pertandingan.store');
    Route::patch('/pertandingan/{pertandingan}/update-score', [PertandinganController::class, 'updateScore']);
});
