<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LostFoundController;
use App\Http\Controllers\FacilityConsumerController;
use App\Http\Controllers\AdminFacilityController;
use Illuminate\Support\Facades\Route;
use App\Models\LostFoundItem;
use Illuminate\Support\Facades\Http;

// ================= ROOT ROUTE =================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// ================= AUTH ROUTES =================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    
    Route::get('/complete-profile', [AuthController::class, 'showCompleteProfile'])->name('auth.complete-profile');
    Route::post('/complete-profile', [AuthController::class, 'storeCompleteProfile'])->name('auth.store-profile');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ================= DASHBOARD / HOME ROUTES =================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Ambil laporan terbaru dari Lost & Found
        $latestLostFound = collect();

        try {
            $latestLostFound = LostFoundItem::latest()
                ->take(3)
                ->get();
        } catch (\Throwable $e) {
            $latestLostFound = collect();
        }

        // Ambil laporan terbaru dari Facility API
        $latestFacilities = collect();

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get('http://127.0.0.1:8001/api/eai/facility-reports');

            if ($response->successful()) {
                $latestFacilities = collect($response->json('data') ?? [])
                    ->take(3);
            }
        } catch (\Throwable $e) {
            $latestFacilities = collect();
        }

        return view('dashboard.index', compact('latestLostFound', 'latestFacilities'));
    })->name('dashboard');
});

// ================= LOST & FOUND ROUTES =================
Route::middleware('auth')->prefix('lost-found')->name('lost-found.')->group(function () {
    Route::get('/', [LostFoundController::class, 'index'])->name('index');
    Route::get('/lost-items', [LostFoundController::class, 'lostIndex'])->name('lost.index');
    
    Route::get('/history-claim', [LostFoundController::class, 'history'])->name('history');
    Route::get('/history-lost', [LostFoundController::class, 'historyLost'])->name('history.lost');
    Route::get('/history-found', [LostFoundController::class, 'historyFound'])->name('history.found');
    
    Route::post('/', [LostFoundController::class, 'store'])->name('store');

    Route::get('/{id}', [LostFoundController::class, 'show'])->name('show');
    
    Route::post('/{id}/verify-report', [LostFoundController::class, 'verifyReport'])->name('verify-report');
    Route::post('/{id}/request-claim', [LostFoundController::class, 'requestClaim'])->name('request-claim');
    Route::post('/{id}/schedule-claim', [LostFoundController::class, 'scheduleClaim'])->name('schedule-claim');
    Route::patch('/{id}/claim', [LostFoundController::class, 'markClaimed'])->name('claim');
    Route::get('/{id}/proof', [LostFoundController::class, 'showProof'])->name('proof');
});

// ================= EAI: USER FACILITY ROUTES =================
Route::middleware('auth')->prefix('fasilitas')->name('facility.')->group(function () {
    Route::get('/', [FacilityConsumerController::class, 'index'])->name('index');
    Route::get('/lapor', [FacilityConsumerController::class, 'create'])->name('create');
    Route::post('/lapor', [FacilityConsumerController::class, 'store'])->name('store');
});

// ================= EAI: ADMIN KELOLA RUANGAN & LAPORAN ROUTES =================
Route::middleware('auth')->prefix('admin/fasilitas')->name('admin.facility.')->group(function () {
    Route::get('/ruangan', [AdminFacilityController::class, 'index'])->name('rooms');
    Route::post('/ruangan', [AdminFacilityController::class, 'storeRoom'])->name('rooms.store');

    Route::get('/laporan', [AdminFacilityController::class, 'indexReports'])->name('reports');
    Route::put('/laporan/{id}', [AdminFacilityController::class, 'updateReport'])->name('reports.update');
});