<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bencana;

class BencanaController extends Controller
{
    public function switchBencana(Request $request)
    {
        $request->validate([
            'bencana_id' => 'required|exists:bencanas,id'
        ]);

        session(['active_bencana_id' => $request->bencana_id]);

        return redirect()->back()->with('success', 'Bencana aktif berhasil diubah.');
    }
}
