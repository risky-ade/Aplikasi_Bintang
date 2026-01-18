<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\HistoriHargaPenjualan;

class HistoriHargaPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'produk_id' => 'nullable|exists:master_produk,id',
            'sumber' => 'nullable|in:produk,penjualan',
            'pelanggan'=> 'nullable|string|max:100',
        ]);

        $histori = HistoriHargaPenjualan::query()
        ->with([
            'produk:id,nama_produk',
            'pelanggan:id,nama'
        ])
        // Filter produk
        ->when($request->produk_id, function ($q) use ($request) {
            $q->where('produk_id', $request->produk_id);
        })
        // Filter sumber perubahan
        ->when($request->sumber, function ($q) use ($request) {
            $q->where('sumber', $request->sumber);
        })
        // Filter pelanggan
        ->when($request->pelanggan, function ($q) use ($request) {
            $q->whereHas('pelanggan', function ($sub) use ($request) {
                $sub->where('nama', 'like', '%' . $request->pelanggan . '%');
            });
        })
        // Filter tanggal
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

        return view('sales.sales_histories.index', compact('histori', 'produk'));
    }
}
