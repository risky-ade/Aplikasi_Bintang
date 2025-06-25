<?php

namespace App\Http\Controllers;

use id;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with(['produk', 'pelanggan'])->get();
        return view('sales.sales_invoices.index', compact('penjualan'));
    }

    public function create()
    {
        return view('sales.sales_invoices.create', [
            'produk' => MasterProduk::all(),
            'pelanggan' => Pelanggan::all(),
            'no_faktur' => 'FJ-' . now()->format('YmdHis'),
            'tanggal' => now()->toDateString(),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pelanggan_id' => 'required',
            'master_produk_id.*' => 'required',
            'qty.*' => 'required|numeric|min:1',
            'harga.*' => 'required|numeric|min:0',
        ]);

        $penjualan = Penjualan::create([
            'no_faktur' => $request->no_faktur,
            'tanggal' => $request->tanggal,
            'pelanggan_id' => $request->pelanggan_id,
            'total' => array_sum($request->subtotal),
            'created_by' => auth()->id(),
        ]);

        foreach ($request->produk_id as $i => $produk_id) {
            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'master_produk_id' => $produk_id,
                'qty' => $request->qty[$i],
                'harga_jual' => $request->harga[$i],
                'subtotal' => $request->subtotal[$i],
            ]);
        }

        return redirect('/sales/sales_invoices')->with('success', 'Transaksi berhasil disimpan');
    }
}
