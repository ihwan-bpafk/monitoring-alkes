<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockDataForReport
{
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan jika hanya melihat data (GET)
        if ($request->isMethod('get')) {
            return $next($request);
        }

        // Cek variabel di .env, jika TRUE maka blokir semua POST, PUT, PATCH, DELETE
        if (config('app.data_locked')) {
            return redirect()->back()->with('error', 'Sistem Terkunci: Data sedang digunakan untuk Laporan Menkes.');
        }

        return $next($request);
    }
}