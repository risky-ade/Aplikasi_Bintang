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
        $no_faktur = 'FPJ-' . date('Ymd') . '/' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT); // strtoupper(uniqid()); //str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
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
        'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
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

    $noSuratJalan = 'SJ-' . str_pad(Penjualan::count() + 1, 5, '0', STR_PAD_LEFT);
    // Simpan ke tabel penjualan
    $penjualan = Penjualan::create([
        'no_faktur'     => $request->no_faktur,
        'no_po'         => $request->no_po,
        'no_surat_jalan' => $noSuratJalan,
        'tanggal'       => $request->tanggal,
        'pelanggan_id'  => $request->pelanggan_id,
        'catatan'       => $request->catatan,
        'pajak'         => $pajak,
        'biaya_kirim'   => $biayaKirim,
        'total'         => $total,
        'jatuh_tempo'   => $request->jatuh_tempo,
        'status_pembayaran' => $request->status_pembayaran,
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
        return view('sales.sales_invoices.show', compact('penjualan'));
    }

    public function edit($id)
    {
        $penjualan = Penjualan::with('detail', 'pelanggan')->findOrFail($id);
        $pelanggan = Pelanggan::all();

        return view('sales.sales_invoices.edit', compact('penjualan', 'pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pelanggan_id' => 'required',
            'produk_id.*' => 'required|exists:master_produk,id',
            'qty.*' => 'required|integer|min:1',
            'harga_jual.*' => 'required|numeric|min:0',
        ]);

        $penjualan = Penjualan::findOrFail($id);

        // Kembalikan stok lama
        foreach ($penjualan->detail as $d) {
            $produk = MasterProduk::find($d->master_produk_id);
            $produk->increment('stok', $d->qty);
        }

        // Hapus detail lama
        $penjualan->detail()->delete();

        // Update data header
        $penjualan->update([
            'tanggal' => $request->tanggal,
            'pelanggan_id' => $request->pelanggan_id,
            'catatan' => $request->catatan,
            'pajak' => $request->pajak ?? 0,
            'biaya_kirim' => $request->biaya_kirim ?? 0,
            'jatuh_tempo' => $request->jatuh_tempo,
            'no_po' => $request->no_po,
            'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
        ]);

        $total = 0;
        foreach ($request->produk_id as $i => $produk_id) {
            $qty = $request->qty[$i];
            $harga = $request->harga_jual[$i];
            $diskon = $request->diskon[$i] ?? 0;
            $subtotal = ($qty * $harga) - $diskon;

            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'master_produk_id' => $produk_id,
                'qty' => $qty,
                'harga_jual' => $harga,
                'diskon' => $diskon,
                'subtotal' => $subtotal,
            ]);

            $produk = MasterProduk::find($produk_id);
            $produk->decrement('stok', $qty);

            $total += $subtotal;
        }

        $pajak = $request->pajak ? ($total * $request->pajak / 100) : 0;
        $total += $pajak + ($request->biaya_kirim ?? 0);

        $penjualan->update(['total' => $total]);

        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil diperbarui.');
    }

    // public function cetak($id)
    // {
    //     $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
    //     return view('penjualan.cetak', compact('penjualan'));
    // }

    public function destroy($id)
    {
        $penjualan = Penjualan::with('detail')->findOrFail($id);

        // Kembalikan stok
        foreach ($penjualan->detail as $item) {
            $produk = MasterProduk::find($item->master_produk_id);
            if ($produk) {
                $produk->increment('stok', $item->qty);
            }
        }

        // Hapus detail & penjualan
        $penjualan->detail()->delete();
        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function suratJalan($id)
    {
        $penjualan = Penjualan::with('detail.produk', 'pelanggan')->findOrFail($id);
        return view('sales.sales_invoices.surat_jalan', compact('penjualan'));
    }

    public function print($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        return view('penjualan.print', compact('penjualan'));

        // $pdf = PDF::loadView('penjualan.print', compact('penjualan'));
        // return $pdf->download('invoice-'.$penjualan->no_faktur.'.pdf');
    }
}
