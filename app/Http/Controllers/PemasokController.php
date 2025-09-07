<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasoks = Pemasok::latest()->paginate(10);
        return view('suppliers.index', compact('pemasoks'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        Pemasok::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pemasok = Pemasok::findOrFail($id);
        return view('suppliers.edit', compact('pemasok'));
    }

    public function update(Request $request, $id)
    {
        $pemasok = Pemasok::findOrFail($id);
        $request->validate([
            'nama' => 'required',
        ]);

        $pemasok->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pemasok = Pemasok::findOrFail($id);
        //cek retur sebelum hapus
        // if ($pelanggan->penjualan && $pelanggan->penjualan->count() > 0) {
        //     return redirect()->route('customers.index')
        //         ->with('error', 'Pelanggan tidak dapat dihapus karena sudah dipakai pada transaksi.');
        // }
        if ($pemasok->pembelian()->exists()) {
            return response()->json([
                'message' => 'Pemasok tidak dapat dihapus karena sudah digunakan dalam transaksi.'
            ], 400);
        }

        $pemasok->delete();

        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil dihapus.');
        // $pelanggan->delete();
        // return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
