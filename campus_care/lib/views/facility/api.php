<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\FacilityReportController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/google', [AuthController::class, 'googleAuth']);

Route::middleware('auth:sanctum')->group(function () {
    // Rute Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Rute Update Profil
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    
    // Rute untuk pengecekan kode aset
    Route::get('/assets/{kode}', [AssetController::class, 'show']);
    // Rute untuk mengambil daftar laporan
    Route::get('/reports', [FacilityReportController::class, 'index']);
    // Rute untuk mengirim laporan kerusakan fasilitas
    Route::post('/reports', [FacilityReportController::class, 'store']);
    // Rute untuk update status laporan (Teknisi/OB)
    Route::put('/reports/{id}/status', [FacilityReportController::class, 'updateStatus']);
});