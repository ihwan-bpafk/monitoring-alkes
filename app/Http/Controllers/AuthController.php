<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Traits\LogsActivity;

class AuthController extends Controller
{
    public function showLogin() 
    {
        return view('auth.login');
    }

    public function login(AuthLoginRequest $request) 
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log Login Activity
            $ip = request()->ip();
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Login',
                'description' => 'User logged in',
                'ip_address' => $ip,
                'location' => LogsActivity::getLocationFromIp($ip),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->intended('repairs');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function logout(Request $request) 
    {
        // Log Logout Activity
        if (Auth::check()) {
            $ip = request()->ip();
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Logout',
                'description' => 'User logged out',
                'ip_address' => $ip,
                'location' => LogsActivity::getLocationFromIp($ip),
                'user_agent' => request()->userAgent(),
            ]);
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}