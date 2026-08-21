<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fasyankes;

class FasyankesController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if($user->name !== 'Administrator', 403, 'Unauthorized access.');
        
        $query = Fasyankes::orderBy('nama_fasyankes');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_fasyankes', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
        }

        $fasyankes = $query->paginate(10)->withQueryString();
        return view('fasyankes.index', compact('fasyankes'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if($user->name !== 'Administrator', 403, 'Unauthorized access.');
        $request->validate([
            'nama_fasyankes' => 'required|string|max:255',
            'jenis' => 'required|string',
            'lokasi' => 'nullable|string'
        ]);

        Fasyankes::create([
            'nama_fasyankes' => $request->nama_fasyankes,
            'jenis' => $request->jenis,
            'lokasi' => $request->lokasi,
            'bencana_id' => session('active_bencana_id')
        ]);

        return redirect()->route('fasyankes.index')->with('success', 'Fasyankes berhasil ditambahkan');
    }

    public function update(Request $request, int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if($user->name !== 'Administrator', 403, 'Unauthorized access.');
        $request->validate([
            'nama_fasyankes' => 'required|string|max:255',
            'jenis' => 'required|string',
            'lokasi' => 'nullable|string'
        ]);

        $fasyankes = Fasyankes::findOrFail($id);
        $fasyankes->update([
            'nama_fasyankes' => $request->nama_fasyankes,
            'jenis' => $request->jenis,
            'lokasi' => $request->lokasi,
        ]);

        return redirect()->route('fasyankes.index')->with('success', 'Fasyankes berhasil diupdate');
    }

    public function destroy(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if($user->name !== 'Administrator', 403, 'Unauthorized access.');
        $fasyankes = Fasyankes::findOrFail($id);
        $fasyankes->delete();

        return redirect()->route('fasyankes.index')->with('success', 'Fasyankes berhasil dihapus');
    }
}
