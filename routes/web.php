<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantSettingController;
use App\Http\Controllers\PlantingHistoryController;

// --- 1. RUTE PUBLIK (Bisa diakses siapa saja) ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/sensor-data', [DashboardController::class, 'getSensorData']);
Route::get('/history', [PlantingHistoryController::class, 'index'])->name('history.index');

// Rute Login AJAX (Untuk SweetAlert)
Route::post('/login-ajax', [AuthController::class, 'loginAjax'])->name('login.ajax');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. RUTE PROTEKSI ADMIN (Hanya bisa 'Write' jika Login) ---
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::post('/settings/update', [PlantSettingController::class, 'update'])->name('settings.update');
    Route::post('/planting/finish', [PlantingHistoryController::class, 'finishSession'])->name('planting.finish');
    Route::delete('/history/{history}', [PlantingHistoryController::class, 'destroy'])->name('history.destroy');
});
