<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBencana
{
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user->bencana_id) {
                // Force user to only their assigned bencana
                if ($request->session()->get('active_bencana_id') != $user->bencana_id) {
                    $request->session()->put('active_bencana_id', $user->bencana_id);
                }
            } else {
                // Admin or all-access user
                if (!$request->session()->has('active_bencana_id')) {
                    // Default to NTT if exists, else first active
                    $defaultBencana = \App\Models\Bencana::where('is_active', true)->where('nama_bencana', 'like', '%NTT%')->first() 
                                    ?? \App\Models\Bencana::where('is_active', true)->first();
                    if ($defaultBencana) {
                        $request->session()->put('active_bencana_id', $defaultBencana->id);
                    }
                }
            }
        }

        return $next($request);
    }
}
