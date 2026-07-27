<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampusAssetController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\EmailVerificationController;

// --- PUBLIC ROUTES (Tanpa perlu login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleAuth']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendCode']);
Route::post('/verify-reset-code', [ForgotPasswordController::class, 'verifyCode']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/verify-register-otp', [EmailVerificationController::class, 'verifyRegisterOtp']);
Route::post('/resend-register-otp', [EmailVerificationController::class, 'resendRegisterOtp']);

// --- EAI ROUTES UNTUK INTEGRASI CAMPUS-WEB ---
Route::post('/eai/sync-user', [AuthController::class, 'syncUserFromWeb']);

// Kalau web mau ambil ruangan tanpa token
Route::get('/eai/rooms', [RoomController::class, 'index']);
Route::post('/eai/rooms', [RoomController::class, 'store']);

// Kalau web mau akses laporan fasilitas tanpa token Sanctum
Route::get('/eai/facility-reports', [FacilityReportController::class, 'eaiIndex']);
Route::post('/eai/facility-reports', [FacilityReportController::class, 'storeFromWeb']);
Route::put('/eai/facility-reports/{id}', [FacilityReportController::class, 'updateFromWeb']);
Route::get('/eai/users/by-email', [AuthController::class, 'getUserByEmail']);

// Lindungi API Pelaporan dengan Middleware Sanctum agar hanya user yang login (dari Flutter) yang bisa akses
Route::middleware('auth:sanctum')->group(function () {
    // --- PROTECTED ROUTES (Hanya bisa diakses jika mengirim Token) ---
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    
    Route::post('/profile/complete', [AuthController::class, 'completeProfile']);

    Route::apiResource('facility-reports', FacilityReportController::class);

    // --- ROOMS MANAGEMENT ---
    Route::get('/rooms/search', [RoomController::class, 'search']);
    Route::apiResource('rooms', RoomController::class);

    // --- ADMIN: Asset Management ---
    Route::apiResource('campus-assets', CampusAssetController::class);
});