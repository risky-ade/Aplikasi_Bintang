<?php

namespace App\Http\Controllers;

use id;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\ReturPenjualan;
use App\Models\PenjualanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriHargaPenjualan;


class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('pelanggan');

    if ($request->filled('no_faktur')) {
        $query->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
    }

    if ($request->filled('no_po')) {
        $query->where('no_po', 'like', '%' . $request->no_po . '%');
    }

    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    }

    if ($request->filled('pelanggan')) {
        $query->whereHas('pelanggan', function ($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->pelanggan . '%');
        });
    }
    if ($request->filled('status_pembayaran')) {
    $query->where('status_pembayaran', $request->status_pembayaran);
    }

    $penjualans = $query->latest()->get();

    return view('sales.sales_invoices.index', compact('penjualans'));
    
    }

    public function create()
    {
        $produk = MasterProduk::all();
        $pelanggan = Pelanggan::all();
        // Generate No Faktur Otomatis
        $lastId = Penjualan::where('tanggal',now()->format('Y-m-d'))->count();
        // dd($lastId);
        $no_faktur = 'FPJ-' . date('Ymd') . '/' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT); // strtoupper(uniqid()); //str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        $tanggal = now()->format('Y-m-d'); // atau date('Y-m-d')

        return view('sales.sales_invoices.create', compact('produk', 'pelanggan', 'no_faktur', 'tanggal', ));
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
        // $test =Auth::id();
        // dd($test);
        
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
        'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
        'created_by'    => Auth::id(),
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
        foreach ($request->produk_id as $i => $produkId) {
            $hargaTransaksi = $request->harga_jual[$i];

            $produk = MasterProduk::find($produkId);
            if ($produk) {
                HistoriHargaPenjualan::create([
                    'produk_id' => $produkId,
                    'pelanggan_id' => $penjualan->pelanggan_id,
                    'harga_lama' => $produk->harga_jual,
                    'harga_baru' => $hargaTransaksi,
                    'sumber' => 'penjualan',
                    'tanggal' => $penjualan->tanggal,
                    'keterangan' => 'Transaksi ke pelanggan ' . $penjualan->pelanggan->nama,
                ]);
            }
        }
        // foreach ($penjualan->detail as $detail) {
        //     HistoriHargaPenjualan::create([
        //         'produk_id' => $detail->master_produk_id,
        //         'pelanggan_id' => $penjualan->pelanggan_id,
        //         'sumber' => 'penjualan',
        //         'harga_lama' => $penjualan->produk->harga_jual,
        //         'harga_baru' => $detail->harga_jual,
        //         'tanggal' => $penjualan->tanggal,
        //         'keterangan' => 'Harga jual ke pelanggan ' . $penjualan->pelanggan->nama,
        //     ]);
        // }
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
        $penjualan = Penjualan::with('detail', 'pelanggan','returPenjualan')->findOrFail($id);
        $pelanggan = Pelanggan::all();
        if ($penjualan->status_pembayaran === 'Lunas') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat diedit atau dihapus karena sudah diset sebagai Lunas.');
        }
        if ($penjualan->returPenjualan && $penjualan->returPenjualan->count() > 0) {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat diedit karena memiliki retur penjualan.');
        }
        // $isReturExists = ReturPenjualan::where('penjualan_id', $penjualan->id)->exists();
        $produk = MasterProduk::all();

        return view('sales.sales_invoices.edit', compact('penjualan', 'pelanggan','isReturExists'));
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
        $penjualan = Penjualan::with('returPenjualan')->findOrFail($id);
        //cek retur sebelum hapus
        if ($penjualan->returPenjualan && $penjualan->returPenjualan->count() > 0) {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat dihapus karena sudah memiliki retur penjualan.');
        }

        if ($penjualan->status_pembayaran === 'Lunas') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat diedit atau dihapus karena sudah diset sebagai Lunas.');
        }

        // Proses hapus detail & stok jika belum ada retur
        foreach ($penjualan->detail as $detail) {
            MasterProduk::where('id', $detail->master_produk_id)->increment('stok', $detail->qty);
            $detail->delete();
        }

        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil dihapus.');
    }

    public function suratJalan($id)
    {
        $penjualan = Penjualan::with('detail.produk', 'pelanggan')->findOrFail($id);
        return view('sales.sales_invoices.surat_jalan', compact('penjualan'));
    }
    public function printSuratJalan($id)
    {
        
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);

        return view('sales.sales_invoices.print_surat_jalan', compact('penjualan'));
        // $pdf = PDF::loadView('penjualan.print_surat_jalan', compact('penjualan'));
        // return $pdf->stream('surat-jalan-' . $penjualan->no_faktur . '.pdf');
    }
    public function suratJalanPdf($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk.satuan'])->findOrFail($id);
        $pdf = Pdf::loadView('sales.sales_invoices.print_surat_jalan', compact('penjualan'))->setPaper('A4', 'portrait');
        $filename = 'surat-jalan-' .preg_replace('/[^A-Za-z0-9\-]/', '-', $penjualan->no_faktur) . '.pdf';
        return $pdf->download($filename);
    }

    public function print($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        return view('sales.sales_invoices.print', compact('penjualan'));

        // $pdf = PDF::loadView('penjualan.print', compact('penjualan'));
        // return $pdf->download('invoice-'.$penjualan->no_faktur.'.pdf');
    }

    public function printPdf($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);

        $pdf = Pdf::loadView('sales.sales_invoices.print', compact('penjualan'))->setPaper('A4', 'portrait');

        $filename = 'Invoice-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $penjualan->no_faktur) . '.pdf';
        return $pdf->download($filename);
        // return $pdf->stream('invoice-' . $penjualan->no_faktur . '.pdf');
        //Gunakan stream() untuk menampilkan langsung di browser, atau download() jika ingin mengunduh otomatis.
    }

    public function approve($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status_pembayaran' => 'Lunas']);
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
        
    }
    public function unapprove($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status_pembayaran === 'Belum Lunas') {
            return back()->with('error', 'Invoice belum lunas.');
        }

        $penjualan->update(['status_pembayaran' => 'Belum Lunas']);

        return back()->with('success', 'Status pembayaran berhasil dibatalkan.');
    }
}
