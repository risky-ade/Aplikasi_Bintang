<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::latest()->paginate(10);
        return view('customers.index', compact('pelanggans'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('customers.edit', compact('pelanggan'));
    }

    public function update(Request $request,$id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $request->validate([
            'nama' => 'required',
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        //cek retur sebelum hapus
        // if ($pelanggan->penjualan && $pelanggan->penjualan->count() > 0) {
        //     return redirect()->route('customers.index')
        //         ->with('error', 'Pelanggan tidak dapat dihapus karena sudah dipakai pada transaksi.');
        // }
        if ($pelanggan->penjualan()->exists()) {
            return response()->json([
                'message' => 'Pelanggan tidak dapat dihapus karena sudah digunakan dalam transaksi.'
            ], 400);
        }

        $pelanggan->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
        // $pelanggan->delete();
        // return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
