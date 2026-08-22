<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alkes;

class AlkesController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if(!in_array($user->name, ['Administrator', 'Prodis Alkes']), 403, 'Unauthorized access.');

        $query = Alkes::orderBy('nama_alkes');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_alkes', 'like', '%' . $request->search . '%');
        }

        $alkes = $query->paginate(10)->withQueryString();
        return view('alkes.index', compact('alkes'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if(!in_array($user->name, ['Administrator', 'Prodis Alkes']), 403, 'Unauthorized access.');
        $request->validate([
            'nama_alkes' => 'required|string|max:255|unique:alkes,nama_alkes',
        ]);

        Alkes::create([
            'nama_alkes' => $request->nama_alkes,
        ]);

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if(!in_array($user->name, ['Administrator', 'Prodis Alkes']), 403, 'Unauthorized access.');
        $request->validate([
            'nama_alkes' => 'required|string|max:255|unique:alkes,nama_alkes,' . $id,
        ]);

        $alkes = Alkes::findOrFail($id);
        $alkes->update([
            'nama_alkes' => $request->nama_alkes,
        ]);

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil diupdate');
    }

    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if(!in_array($user->name, ['Administrator', 'Prodis Alkes']), 403, 'Unauthorized access.');
        $alkes = Alkes::findOrFail($id);
        $alkes->delete();

        return redirect()->route('alkes.index')->with('success', 'Master Alkes berhasil dihapus');
    }
}
