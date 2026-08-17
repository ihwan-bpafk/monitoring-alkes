<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alkes;

class AlkesController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $alkes = Alkes::orderBy('nama_alkes')->paginate(10);
        return view('alkes.index', compact('alkes'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $request->validate([
            'nama_alkes' => 'required|string|max:255|unique:alkes,nama_alkes',
        ]);

        Alkes::create([
            'nama_alkes' => $request->nama_alkes,
        ]);

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $request->validate([
            'nama_alkes' => 'required|string|max:255|unique:alkes,nama_alkes,'.$id,
        ]);

        $alkes = Alkes::findOrFail($id);
        $alkes->update([
            'nama_alkes' => $request->nama_alkes,
        ]);

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil diupdate');
    }

    public function destroy($id)
    {
        abort_if(auth()->user()->role !== 1, 403, 'Unauthorized access.');
        $alkes = Alkes::findOrFail($id);
        $alkes->delete();

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil dihapus');
    }
}
