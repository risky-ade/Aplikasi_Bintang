<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\HistoriHargaPembelian;

class HistoriHargaPembelianController extends Controller
{
    private function filteredQuery(Request $request)
    {
        return HistoriHargaPembelian::query()
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
            });
    }

    public function index(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'produk_id' => 'nullable|exists:master_produk,id',
            'sumber' => 'nullable|in:produk,pembelian',
            'pemasok'=> 'nullable|string|max:100'
        ]);
        $histori = $this->filteredQuery($request)
        ->with([
            'produk:id,nama_produk',
            'pemasok:id,nama'
        ])
        ->latest('tanggal')
        ->get();

        $produk = MasterProduk::select('id', 'nama_produk')
        ->orderBy('nama_produk')
        ->get();

        return view('purchases.purchases_histories.index', compact('histori', 'produk'));
    }

    public function destroySelected(Request $request)
    {
        $request->validate([
            'histori_ids' => 'required|array|min:1',
            'histori_ids.*' => 'integer|exists:histori_harga_pembelian,id',
        ]);

        $deleted = HistoriHargaPembelian::whereIn('id', $request->histori_ids)->delete();

        return back()->with('success', $deleted . ' histori harga pembelian berhasil dihapus.');
    }

    public function destroyByDate(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'produk_id' => 'nullable|exists:master_produk,id',
            'pemasok'=> 'nullable|string|max:100',
        ]);

        $deleted = $this->filteredQuery($request)->delete();

        return back()->with('success', $deleted . ' histori harga pembelian berhasil dihapus sesuai rentang tanggal.');
    }
}
