<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantSettingController;
use App\Http\Controllers\PlantingHistoryController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/sensor-data', [DashboardController::class, 'getSensorData']);

// Route untuk Update Setting
Route::post('/settings/update', [PlantSettingController::class, 'update'])->name('settings.update');

// Panen / Finish Session (Tombol Merah)
Route::post('/planting/finish', [PlantingHistoryController::class, 'finishSession'])->name('planting.finish');
Route::get('/history', [PlantingHistoryController::class, 'index'])->name('history.index');
