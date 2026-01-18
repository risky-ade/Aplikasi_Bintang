<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\HistoriHargaPembelian;

class HistoriHargaPembelianController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'produk_id' => 'nullable|exists:master_produk,id',
            'sumber' => 'nullable|in:produk,pembelian',
            'pemasok'=> 'nullable|string|max:100'
        ]);
        $histori = HistoriHargaPembelian::query()
        ->with([
            'produk:id,nama_produk',
            'pemasok:id,nama'
        ])
        ->when($request->produk_id, function ($q) use ($request) {
            $q->where('produk_id', $request->produk_id);
        })
        ->when($request->pemasok, function ($q) use ($request) {
            $q->whereHas('pemasok', function ($sub) use ($request) {
                $sub->where('nama', 'like', '%' . $request->pemasok . '%');
            });
        })
        ->when($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'), function ($q) use ($request) {
            $q->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        })
        ->when($request->filled('tanggal_awal') && !$request->filled('tanggal_akhir'), function ($q) use ($request) {
            $q->whereDate('tanggal', '>=', $request->tanggal_awal);
        })
        ->when(!$request->filled('tanggal_awal') && $request->filled('tanggal_akhir'), function ($q) use ($request) {
            $q->whereDate('tanggal', '<=', $request->tanggal_akhir);
        })
        ->latest('tanggal')
        ->get();

        $produk = MasterProduk::select('id', 'nama_produk')
        ->orderBy('nama_produk')
        ->get();

        return view('purchases.purchases_histories.index', compact('histori', 'produk'));
    }
}
