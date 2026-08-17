<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fasyankes;

class FasyankesController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $fasyankes = Fasyankes::orderBy('nama_fasyankes')->paginate(10);
        return view('fasyankes.index', compact('fasyankes'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
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

    public function update(Request $request, $id)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
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

    public function destroy($id)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $fasyankes = Fasyankes::findOrFail($id);
        $fasyankes->delete();

        return redirect()->route('fasyankes.index')->with('success', 'Fasyankes berhasil dihapus');
    }
}
