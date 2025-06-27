<?php

namespace App\Http\Controllers;

use id;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with('pelanggan')->orderBy('tanggal', 'desc')->get();
        return view('sales.sales_invoices.index', compact('penjualans'));
    }

    public function create()
    {
        $produk = MasterProduk::all();
        $pelanggan = Pelanggan::all();
        // Generate No Faktur Otomatis
        $lastId = Penjualan::max('id') ?? 0;
        $no_faktur = 'FP-' . date('Ymd') . '/' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT); // strtoupper(uniqid()); //str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        $tanggal = now()->format('Y-m-d'); // atau date('Y-m-d')

        return view('sales.sales_invoices.create', compact('produk', 'pelanggan', 'no_faktur', 'tanggal', ));
        // return view('sales.sales_invoices.create', [
        //     'master_produk' => MasterProduk::all(),
        //     'pelanggan' => Pelanggan::all(),
        //     'no_faktur' => 'FJ-' . now()->format('YmdHis'),
        //     'tanggal' => now()->toDateString(),
        // ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|unique:penjualan,no_faktur',
            'tanggal' => 'required|date',
            'pelanggan_id' => 'required',
            'produk_id' => 'required|array',
            'produk_id.*' => 'required|exists:master_produk,id',
            'qty.*' => 'required|integer|min:1',
            'harga_jual.*' => 'required|numeric|min:0',
        ]);

        // Simpan ke tabel penjualan
    $penjualan = Penjualan::create([
        'no_faktur' => $request->no_faktur,
        'tanggal' => $request->tanggal,
        'pelanggan_id' => $request->pelanggan_id,
        'biaya_kirim' => $request->biaya_kirim,
        'jatuh_tempo' => $request->jatuh_tempo,
        'total' => 0, // total sementara, nanti dihitung
        'created_by' => 1
    ]);

    $total = 0;
    foreach ($request->produk_id as $index => $produk_id) {
        $qty = $request->qty[$index];
        $harga = $request->harga_jual[$index];
        $diskon = $request->diskon[$index]?? 0;
        $subtotal = ($qty * $harga)- $diskon;

        // Simpan detail
        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'master_produk_id' => $produk_id,
            'qty' => $qty,
            'harga_jual' => $harga,
            'diskon' => $diskon,
            'subtotal' => $subtotal,
        ]);
        // Kurangi stok produk
        $produk = MasterProduk::find($produk_id);

        if ($produk->stok < $qty) {      
            return back()->with('error', 'Stok produk "' . $produk->nama_produk . '" tidak mencukupi. Tersedia: ' . $produk->stok);
        }else{
            $produk->decrement('stok', $qty);
        }

        $total += $subtotal;
    }
    // Tambahkan biaya kirim
    $total += $request->biaya_kirim;

    // Update total
    $penjualan->update(['total' => $total]);

    return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil disimpan.');

     
    }

     public function show($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }

    public function cetak($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        return view('penjualan.cetak', compact('penjualan'));
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        // Kembalikan stok saat data dihapus
        foreach ($penjualan->detail as $item) {
            $produk = MasterProduk::find($item->produk_id);
            $produk->stok += $item->qty;
            $produk->save();
        }

        PenjualanDetail::where('penjualan_id', $id)->delete();
        $penjualan->delete();

        return redirect()->route('sales.sales_invoices.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
