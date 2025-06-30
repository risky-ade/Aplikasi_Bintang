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

    $subtotal = 0;
    $totalDiskon = 0;

    foreach ($request->produk_id as $index => $produk_id) {
        $qty = $request->qty[$index];
        $harga = $request->harga_jual[$index];
        $diskon = $request->diskon[$index] ?? 0;
        $sub = ($qty * $harga) - $diskon;

        $subtotal += $sub;
        $totalDiskon += $diskon;
    }

    $pajak = $request->pajak ?? 0;
    $biayaKirim = $request->biaya_kirim ?? 0;
    $totalPajak = ($subtotal * $pajak) / 100;

    $total = $subtotal + $totalPajak + $biayaKirim;

    // Simpan ke tabel penjualan
    $penjualan = Penjualan::create([
        'no_faktur'     => $request->no_faktur,
        'tanggal'       => $request->tanggal,
        'pelanggan_id'  => $request->pelanggan_id,
        'catatan'       => $request->catatan,
        'pajak'         => $pajak,
        'biaya_kirim'   => $biayaKirim,
        'total'         => $total,
        'jatuh_tempo'   => $request->jatuh_tempo,
        'created_by'    => 1
    ]);

    // Proses detail penjualan & stok
    foreach ($request->produk_id as $index => $produk_id) {
        $qty = $request->qty[$index];
        $harga = $request->harga_jual[$index];
        $diskon = $request->diskon[$index] ?? 0;
        $sub = ($qty * $harga) - $diskon;

        // Validasi stok
        $produk = MasterProduk::find($produk_id);
        if ($produk->stok < $qty) {
            return back()->with('error', 'Stok produk "' . $produk->nama_produk . '" tidak mencukupi. Tersedia: ' . $produk->stok);
        }

        // Simpan detail
        PenjualanDetail::create([
            'penjualan_id'      => $penjualan->id,
            'master_produk_id'  => $produk_id,
            'qty'               => $qty,
            'harga_jual'        => $harga,
            'diskon'            => $diskon,
            'subtotal'          => $sub,
        ]);

        // Kurangi stok
        $produk->decrement('stok', $qty);
    }

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
