<?php

namespace App\Http\Controllers;

use id;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\ReturPenjualan;
use Illuminate\Support\Carbon;
use App\Models\PenjualanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriHargaPenjualan;


class PenjualanController extends Controller
{
    public function index(Request $request)
    {
         $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);
        // $query = Penjualan::with('pelanggan');
        $query = Penjualan::query()
            ->with(['pelanggan'])
            ->select('penjualan.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_penjualan as r')
                    ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('r.penjualan_id', 'penjualan.id');
            }, 'total_retur')
            // total netto = total - total_retur (ulang subquery supaya kompatibel dengan MySQL alias)
            ->selectSub(function ($sub) {
                $sub->from('retur_penjualan as r')
                    ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id') // GANTI kalau perlu
                    ->whereColumn('r.penjualan_id', 'penjualan.id')
                    ->selectRaw('GREATEST(0, penjualan.total - COALESCE(SUM(rd.subtotal), 0))');
                // ->withSum(['returPenjualan as total_retur' => fn($q)=>$q/*->where('status','!=','batal')*/], 'total')
                // ->selectRaw('GREATEST(0, total - IFNULL((select SUM(total) from retur_penjualan r where r.penjualan_id = penjualan.id),0)) as total_netto');
            }, 'total_netto');

    if ($request->filled('no_faktur')) {
        $query->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
    }

    if ($request->filled('no_po')) {
        $query->where('no_po', 'like', '%' . $request->no_po . '%');
    }

    // if ($request->filled('tanggal')) {
    //     $query->whereDate('tanggal', $request->tanggal);
    // }

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
    } elseif ($request->filled('tanggal_awal')) {
        $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    } elseif ($request->filled('tanggal_akhir')) {
        $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
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
    // Recompute Total Netto yang benar (pajak dihitung dari subtotal net)
    foreach ($penjualans as $p) {
        $pajak = (float) ($p->pajak ?? 0);
        $ongkir = (float) ($p->biaya_kirim ?? 0);
        $totalRetur = (float) ($p->total_retur ?? 0);

        // subtotal_bruto = (total - ongkir) / (1 + pajak%)
        $den = 1 + ($pajak / 100);
        $subtotalBruto = $den != 0 ? (($p->total - $ongkir) / $den) : ($p->total - $ongkir);

        // subtotal_net = max(0, subtotal_bruto - total_retur)
        $subtotalNet = max(0, $subtotalBruto - $totalRetur);

        // pajak_net = subtotal_net * pajak%
        $pajakNet = $subtotalNet * ($pajak / 100);

        // total_netto yang benar
        $p->total_netto_calc = $subtotalNet + $pajakNet + $ongkir;
    }

    return view('sales.sales_invoices.index', compact('penjualans'));
    
    }

    public function create()
    {
        $produk = MasterProduk::all();
        $pelanggan = Pelanggan::all();
        // Generate No Faktur Otomatis
        $lastId = Penjualan::where('tanggal',now()->format('Y-m-d'))->count();
        // dd($lastId);
        $no_faktur = 'FPJ-' . date('Ymd') . '/' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
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
        // dd($request);
    $subtotal = 0;
    $totalDiskon = 0;

    foreach ($request->produk_id as $index => $produk_id) {
        $qty = $request->qty[$index];
        $harga = $request->harga_jual[$index];
        $diskon = $request->diskon[$index] ?? 0;
        $sub = ($qty * $harga - $qty * $diskon);

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
// dd($penjualan);
    // Proses detail penjualan & stok
    foreach ($request->produk_id as $index => $produk_id) {
        $qty = $request->qty[$index];
        $harga = $request->harga_jual[$index];
        $diskon = $request->diskon[$index] ?? 0;
        $sub = ($qty * $harga - $qty * $diskon);

        // Validasi stok
        $produk = MasterProduk::find($produk_id);
        if ($produk->stok < $qty) {
            return back()->with('error', 'Stok produk ' . $produk->nama_produk . ' tidak mencukupi. Tersedia: ' . $produk->stok);
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

        //  Cek hanya buat histori jika harga berbeda dengan harga default produk
        $lastHistori = HistoriHargaPenjualan::where('produk_id', $produk->id)
            ->latest('id')
            ->first();
        if ($harga != $produk->harga_jual && (!$lastHistori || $lastHistori->harga_baru != $harga)) {
            $tanggalHist = $penjualan->getRawOriginal('tanggal');
            HistoriHargaPenjualan::create([
                'produk_id' => $produk->id,
                'pelanggan_id'     => $penjualan->pelanggan_id,
                'harga_lama'       => $produk->harga_jual,
                'harga_baru'       => $harga,
                'sumber'           => 'penjualan',
                'tanggal'          => $tanggalHist,
                'keterangan'       => 'Perubahan harga saat transaksi Faktur ' . $penjualan->no_faktur,
            ]);
        }
        // Kurangi stok
        $produk->decrement('stok', $qty);
    }
    return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil disimpan.');
     
    }

     public function show($id)
    {
        $penjualan = Penjualan::with(['pelanggan','detail.produk'])
            ->withSum('returPenjualan as total_retur', 'total')
            ->findOrFail($id);

        
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();
// dd($produkDiretur, $penjualan->detail->pluck('produk_id')); 
        $totalRetur = (float) ($penjualan->total_retur ?? 0);
        $totalNetto = max(0, (float) ($penjualan->total ?? 0) - $totalRetur);

        return view('sales.sales_invoices.show', compact('penjualan','produkDiretur','totalRetur', 'totalNetto'));
    }

    public function edit($id)
    {
        $penjualan = Penjualan::with('detail', 'pelanggan','returPenjualan')->findOrFail($id);
        // dd($penjualan);
        $pelanggan = Pelanggan::all();
        if ($penjualan->status_pembayaran === 'Lunas') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat diedit atau dihapus karena sudah diset sebagai Lunas.');
        }
        if ($penjualan->returPenjualan->count() > 0) {
            return redirect()->route('penjualan.index')
                ->with('error', 'Faktur tidak dapat diedit karena memiliki retur penjualan.');
        }
        $isReturExists = ReturPenjualan::where('penjualan_id', $penjualan->id)->exists();
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

        $penjualan = Penjualan::with('detail')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Kembalikan stok lama sebelum update
            foreach ($penjualan->detail as $d) {
                $produk = MasterProduk::find($d->master_produk_id);
                if ($produk) {
                    $produk->increment('stok', $d->qty);
                }
            }

            //  Hapus detail lama
            $penjualan->detail()->delete();

            //  Update header faktur
            $penjualan->update([
                'tanggal'           => $request->tanggal,
                'pelanggan_id'      => $request->pelanggan_id,
                'catatan'           => $request->catatan,
                'pajak'             => $request->pajak ?? 0,
                'biaya_kirim'       => $request->biaya_kirim ?? 0,
                'jatuh_tempo'       => $request->jatuh_tempo,
                'no_po'             => $request->no_po,
                'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
            ]);

            $total = 0;

            foreach ($request->produk_id as $i => $produk_id) {
                $qty     = $request->qty[$i];
                $harga   = $request->harga_jual[$i];
                $diskon  = $request->diskon[$i] ?? 0;
                $subtotal = ($qty * $harga - $qty * $diskon);

                //  Validasi stok
                $produk = MasterProduk::findOrFail($produk_id);
                if ($produk->stok < $qty) {
                    DB::rollBack();
                    return back()->with('error', 'Stok produk "' . $produk->nama_produk . '" tidak mencukupi. Tersedia: ' . $produk->stok);
                }

                //  Simpan detail baru
                PenjualanDetail::create([
                    'penjualan_id'     => $penjualan->id,
                    'master_produk_id' => $produk_id,
                    'qty'              => $qty,
                    'harga_jual'       => $harga,
                    'diskon'           => $diskon,
                    'subtotal'         => $subtotal,
                ]);

                //  Kurangi stok sesuai qty baru
                $produk->decrement('stok', $qty);
                $tanggalHist = $penjualan->getRawOriginal('tanggal') ?: \Carbon\Carbon::parse($request->tanggal)->toDateString();
                //Cek histori harga terakhir produk ini
                $lastHistori = HistoriHargaPenjualan::where('produk_id', $produk->id)
                    ->where('pelanggan_id', $penjualan->pelanggan_id)
                    ->latest('created_at')
                    ->first();

                $hargaLama = $lastHistori ? $lastHistori->harga_baru : $produk->harga_jual;
                //Catat hanya jika harga BERBEDA dari histori terakhir
                if ($harga != $hargaLama) {
                    HistoriHargaPenjualan::create([
                        'produk_id' => $produk->id,
                        'pelanggan_id'     => $penjualan->pelanggan_id,
                        'harga_lama'       => $produk->harga_jual,
                        'harga_baru'       => $harga,
                        'sumber'           => 'penjualan',
                        'tanggal'          => $tanggalHist,
                        'keterangan'       => 'Perubahan harga saat UPDATE Faktur ' . $penjualan->no_faktur,
                    ]);
                }

                $total += $subtotal;
            }

            //  Hitung ulang total faktur
            $pajak = $request->pajak ? ($total * $request->pajak / 100) : 0;
            $total += $pajak + ($request->biaya_kirim ?? 0);

            $penjualan->update(['total' => $total]);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update faktur: ' . $e->getMessage());
        }
    }

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
        // $penjualan = Penjualan::with('detail.produk', 'pelanggan')->findOrFail($id);
        $penjualan = Penjualan::with(['pelanggan','detail.produk'])
            ->withSum('returPenjualan as total_retur', 'total')
            ->findOrFail($id);
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();
        return view('sales.sales_invoices.surat_jalan', compact('penjualan','produkDiretur'));
    }
    public function printSuratJalan($id)
    {
        
        // $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        $penjualan = Penjualan::with(['pelanggan','detail.produk'])
            ->withSum('returPenjualan as total_retur', 'total')
            ->findOrFail($id);
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        return view('sales.sales_invoices.print_surat_jalan', compact('penjualan','produkDiretur'));
        // $pdf = PDF::loadView('penjualan.print_surat_jalan', compact('penjualan'));
        // return $pdf->stream('surat-jalan-' . $penjualan->no_faktur . '.pdf');
    }
    public function suratJalanPdf($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detail.produk.satuan'])->findOrFail($id);
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();
        $pdf = Pdf::loadView('sales.sales_invoices.print_surat_jalan', compact('penjualan','produkDiretur'))->setPaper('A4', 'portrait');
        $filename = 'surat-jalan-' .preg_replace('/[^A-Za-z0-9\-]/', '-', $penjualan->no_faktur) . '.pdf';
        return $pdf->download($filename);
    }

    public function print($id)
    {
        // $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        $penjualan = Penjualan::with(['pelanggan','detail.produk'])
            ->withSum('returPenjualan as total_retur', 'total') // jika tabel retur punya kolom total
            ->findOrFail($id);
        // Pluck qty retur per produk
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        // $totalRetur = (float)($penjualan->total_retur ?? 0);
        // $totalNetto = max(0, (float)$penjualan->total - $totalRetur);
       
        
        return view('sales.sales_invoices.print', compact('penjualan','produkDiretur'));

        // $pdf = PDF::loadView('penjualan.print', compact('penjualan'));
        // return $pdf->download('invoice-'.$penjualan->no_faktur.'.pdf');
    }

    public function printPdf($id)
    {
        // $penjualan = Penjualan::with(['pelanggan', 'detail.produk'])->findOrFail($id);
        $penjualan = Penjualan::with(['pelanggan','detail.produk'])
            ->withSum('returPenjualan as total_retur', 'total') // jika tabel retur punya kolom total
            ->findOrFail($id);
        $produkDiretur = DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualan->id)
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
            ->groupBy('rd.produk_id')
            ->pluck('total_qty_retur', 'rd.produk_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        $pdf = Pdf::loadView('sales.sales_invoices.print', compact('penjualan','produkDiretur'))->setPaper('A4', 'portrait');

        $filename = 'Invoice-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $penjualan->no_faktur) . '.pdf';
        return $pdf->download($filename);
        // return $pdf->stream('invoice-' . $penjualan->no_faktur . '.pdf');
        //Gunakan stream() untuk menampilkan langsung di browser, atau download() jika ingin mengunduh otomatis.
    }

    public function approve($id, Request $request)
    {
        $request->validate([
        'paid_date' => ['required','date_format:Y-m-d'],
        ],[
            'paid_date.required' => 'Tanggal pelunasan wajib diisi.',
        ]);
        $penjualan = Penjualan::findOrFail($id);

        $paidDate = Carbon::parse($request->paid_date)
                ->setTimeFromTimeString(now()->format('H:i:s'));
        $penjualan->update([
            'status_pembayaran' => 'Lunas',
            'approved_at' =>now('Asia/Jakarta'),
            'paid_date'         => $paidDate,
            'approved_by'        => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas per ' . Carbon::parse($paidDate)->format('d/m/Y') . '.');
        
    }
    public function unapprove($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status_pembayaran === 'Belum Lunas') {
            return back()->with('error', 'Invoice belum lunas.');
        }
        if (!$penjualan->approved_at) {
            return back()->with('error', 'Waktu persetujuan tidak ditemukan, tidak dapat dibatalkan.');
        }
        // Hitung selisih menit sejak approved
        $selisihMenit = $penjualan->approved_at->diffInMinutes(now());
        $batasMenit = 60 * 24;

        if ($selisihMenit > $batasMenit) {
            return back()->with(
                'error',
                "Batas pembatalan pelunasan sudah lewat {$batasMenit} menit, tidak bisa dibatalkan."
            );
        }

        $penjualan->update([
            'status_pembayaran' => 'Belum Lunas',
            'approved_at' => null,
            'paid_date'   => null,
        ]);

        return back()->with('success', 'Pelunasan berhasil dibatalkan.');
    }

    public function batal($id)
    {
        $penjualan = Penjualan::with('detail.produk')->findOrFail($id);

        // Cegah jika status pembayaran sudah Lunas
        if ($penjualan->status_pembayaran === 'Lunas') {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah dibayar.');
        }
        // Cegah jika sudah ada retur penjualan
        if ($penjualan->returPenjualan && $penjualan->returPenjualan->count() > 0) {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah memiliki retur penjualan.');
        }
        if ($penjualan->status === 'batal') {
            return back()->with('error', 'Faktur sudah dibatalkan sebelumnya.');
        }
 
        // Rollback stok
        foreach ($penjualan->detail as $item) {
            $produk = $item->produk;
            $produk->increment('stok', $item->qty);
        }

        $penjualan->update([
            'status' => 'batal'
        ]);

        // return response()->json(['message' => 'Faktur berhasil dibatalkan.']);
        return redirect()->route('penjualan.index')->with('success', 'Faktur berhasil dibatalkan.');
    }


}
