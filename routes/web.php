<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/sensor-data', [DashboardController::class, 'getSensorData']);

// Route untuk Update Setting
Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('settings.update');
