<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DistributionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. Rute Otentikasi (Public) ---
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// --- 2. Rute Terproteksi (Hanya untuk User Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Pengalihan Halaman Utama
    Route::get('/', function () { return redirect()->route('dashboard'); });

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.exportExcel');

    // --- REPAIRS (PERBAIKAN ALKES) ---
    Route::resource('repairs', RepairController::class);
    Route::post('repairs/{id}/update-status', [RepairController::class, 'updateStatus'])->name('repairs.updateStatus');
    Route::get('/repairs/export', [RepairController::class, 'exportExcel'])->name('repairs.export');
    Route::get('/repairs/report', [RepairController::class, 'reportPage'])->name('repairs.report');
    Route::get('/repairs/report-preview', [RepairController::class, 'previewExport'])->name('repairs.reportPreview');

    // --- DONATIONS (STOK MASUK) ---
    // Menggunakan Resource karena mencakup Index, Store, Update, dan Destroy
    Route::resource('donations', DonationController::class);
    Route::patch('/donations/{id}/status', [DonationController::class, 'updateStatus'])->name('donations.updateStatus');

    // --- DISTRIBUTIONS (ALOKASI KE RS) ---
    // Rute manual untuk menangani logika pengurangan stok yang spesifik
    Route::resource('distributions', DistributionController::class);
    
    // Rute untuk memperbarui status pengiriman (Dikirim -> Diterima)
    Route::patch('/distributions/{id}/status', [DistributionController::class, 'updateStatus'])->name('distributions.updateStatus');
});