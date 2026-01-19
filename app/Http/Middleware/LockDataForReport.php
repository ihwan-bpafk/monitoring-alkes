<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockDataForReport
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Izinkan kalau cuma lihat data (GET)
        if ($request->isMethod('get')) {
            return $next($request);
        }

        // 2. Cek apakah sistem dikunci DAN user yang login BUKAN 'admin'
        // Ganti 'admin' dengan username admin Ahmad yang sebenarnya jika berbeda
        if (config('app.data_locked') && auth()->user()->username !== 'admin') {
            return redirect()->back()->with('error', 'Sistem Terkunci: Hanya Admin Utama yang boleh mengubah data selama masa laporan.');
        }

        return $next($request);
    }
}