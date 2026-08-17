<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\FasyankesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. Rute Otentikasi (Public) ---
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('switch-bencana', [\App\Http\Controllers\BencanaController::class, 'switchBencana'])->name('switchBencana')->middleware('auth');


// --- 2. Rute Terproteksi (Hanya untuk User Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Pengalihan Halaman Utama
    Route::get('/', function () { return redirect()->route('dashboard'); });

    // --- RUTE READ-ONLY (Selalu Bisa Diakses untuk Laporan) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.exportExcel');

    Route::get('/repairs/export', [RepairController::class, 'exportExcel'])->name('repairs.export');
    Route::get('/repairs/report', [RepairController::class, 'reportPage'])->name('repairs.report');
    Route::get('/repairs/report-preview', [RepairController::class, 'previewExport'])->name('repairs.reportPreview');
    
    Route::get('donations/export-excel', [DonationController::class, 'exportExcel'])->name('donations.export');
    Route::get('distributions/export-excel', [DistributionController::class, 'exportExcel'])->name('distributions.export');

    // --- RUTE DENGAN PROTEKSI LOCK DATA (Mencegah Update/Input saat Laporan Menkes) ---
    // Middleware 'lock.report' akan memblokir method POST, PATCH, PUT, dan DELETE jika APP_DATA_LOCKED=true
    Route::middleware(['lock.report'])->group(function () {
        
        // REPAIRS
        Route::post('repairs/{id}/update-status', [RepairController::class, 'updateStatus'])->name('repairs.updateStatus');
        Route::resource('repairs', RepairController::class);

        // DONATIONS
        Route::resource('donations', DonationController::class)->except(['index', 'show']);
        Route::patch('/donations/{id}/status', [DonationController::class, 'updateStatus'])->name('donations.updateStatus');

        // DISTRIBUTIONS
        Route::resource('distributions', DistributionController::class)->except(['index', 'show']);
        Route::patch('/distributions/{id}/status', [DistributionController::class, 'updateStatus'])->name('distributions.updateStatus');
        
        // FASYANKES
        Route::resource('fasyankes', FasyankesController::class)->except(['create', 'show', 'edit', 'index']);
    });

    // Rute Index tetap di luar Lock agar data bisa dilihat
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/distributions', [DistributionController::class, 'index'])->name('distributions.index');
    
    // Fasyankes
    Route::get('/fasyankes', [FasyankesController::class, 'index'])->name('fasyankes.index');
});