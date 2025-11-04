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
            'sumber' => 'nullable|in:produk,pembelian'
        ]);
        $query = HistoriHargaPembelian::with('produk','pemasok');

        // Filter produk
        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        // Filter sumber (transaksi atau master produk)
        if ($request->filled('sumber')) {
            $query->where('sumber', $request->sumber);
        }
        if ($request->filled('pemasok')) {
            $query->whereHas('pemasok', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->pemasok . '%');
            });
        }
        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        } elseif ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $histori = $query->latest('tanggal')->orderBy('created_at', 'desc')->paginate(10);
        $produk = MasterProduk::orderBy('nama_produk')->get();

        return view('purchases.purchases_histories.index', compact('histori', 'produk'));
        // $histories = HistoriHargaPenjualan::with('produk')->latest()->paginate(15);
        // return view('sales.sales_histories.index', compact('histories'));
    }
}
