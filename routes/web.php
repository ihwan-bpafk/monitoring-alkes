<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat mendaftarkan semua rute untuk aplikasi Monitoring 
| Perbaikan Alkes Anda.
|
*/

/**
 * 1. Pengalihan Halaman Utama
 * Saat user membuka domain utama, otomatis diarahkan ke daftar perbaikan.
 */
// Route Auth
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', function () { return redirect()->route('repairs.index'); });
// Route::resource('repairs', RepairController::class);
// Route::post('repairs/{id}/update-status', [RepairController::class, 'updateStatus'])->name('repairs.updateStatus');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () { return redirect()->route('repairs.index'); });
    Route::resource('repairs', RepairController::class);
    Route::post('repairs/{id}/update-status', [RepairController::class, 'updateStatus'])->name('repairs.updateStatus');
});