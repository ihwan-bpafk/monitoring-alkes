<?php

namespace App\Observers;

use App\Models\Donation;
use App\Models\DonationLog;
use Illuminate\Support\Facades\Auth;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        // Cek siapa yang menginput (jika dari terminal/CLI, Auth::user() akan kosong)
        $user = Auth::check() ? Auth::user()->name : 'System (Excel Import)';

        DonationLog::create([
            'donation_id' => $donation->id,
            'status' => 'Data Masuk',
            'diupdate_oleh' => $user,
            'catatan' => "Data pertama kali tercatat di sistem (Sumber: " . (Auth::check() ? 'Manual Input' : 'Excel Import') . ").",
        ]);
    }
}