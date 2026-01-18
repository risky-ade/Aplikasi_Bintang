<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriHargaPembelian;
use App\Models\ProfilePerusahaan;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
         $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);
        // $query = Pembelian::with('pemasok');
        $query = Pembelian::query()
            ->with(['pemasok'])
            ->select('pembelian.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_pembelian as rb')
                    ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'rb.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('rb.pembelian_id', 'pembelian.id');
            }, 'total_retur')
            // total netto = total - total_retur (ulang subquery supaya kompatibel dengan MySQL alias)
            ->selectSub(function ($sub) {
                $sub->from('retur_pembelian as rb')
                    ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'rb.id') // GANTI kalau perlu
                    ->whereColumn('rb.pembelian_id', 'pembelian.id')
                    ->selectRaw('GREATEST(0, pembelian.total - COALESCE(SUM(rd.subtotal), 0))');
                // ->withSum(['returPenjualan as total_retur' => fn($q)=>$q/*->where('status','!=','batal')*/], 'total')
                // ->selectRaw('GREATEST(0, total - IFNULL((select SUM(total) from retur_penjualan r where r.penjualan_id = penjualan.id),0)) as total_netto');
            }, 'total_netto');


        if ($request->filled('no_faktur')) {
            $query->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
        }
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        } elseif ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('pemasok')) {
            $query->whereHas('pemasok', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->pemasok . '%');
            });
        }
        if ($request->filled('status_pembayaran')) {
        $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pembelians = $query->latest()->get();
        // Recompute Total Netto yang benar (pajak dihitung dari subtotal net)
        foreach ($pembelians as $p) {
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
            $p->save();
        }

            return view('purchases.purchase_inv.index', compact('pembelians'));
    }

    public function create()
    {
        $produk = MasterProduk::all();
        $pemasok = Pemasok::orderBy('nama')->get();
        $tanggal = now()->format('Y-m-d');
        $no_faktur = $this->generateNoFaktur();
        return view('purchases.purchase_inv.create', compact('produk','pemasok','tanggal','no_faktur'));
    }

    protected function generateNoFaktur(): string
    {
        $prefix = 'FPB-'.now()->format('Ymd').'/';
        $urut = (Pembelian::whereDate('created_at', now()->toDateString())->count() + 1);
        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pemasok_id' => 'required|exists:pemasok,id',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:master_produk,id',
            'qty.*' => 'required|integer|min:1',
            'harga_beli.*' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0; 
            $totalDiskon = 0;
            foreach ($request->produk_id as $i => $pid) {
                $qty = (int) $request->qty[$i];
                $harga = $request->harga_beli[$i];
                $diskon = ($request->diskon[$i] ?? 0);
                $subtotal += ($qty * $harga - $qty * $diskon);
                $totalDiskon += $diskon;
            }
            $diskonNota = (float) ($request->diskon_nota ?? 0);
            $subtotDisc = max(0, $subtotal - $diskonNota);

            $pajakPersen = ($request->pajak ?? 0);
            $biayaKirim  = ($request->biaya_kirim ?? 0);
            $totalPajak  = $subtotDisc * $pajakPersen / 100;
            $total       = $subtotDisc + $totalPajak + $biayaKirim;

            $pembelian = Pembelian::create([
                'no_faktur'         => $this->generateNoFaktur(),
                'no_po'             => $request->no_po,
                'tanggal'           => $request->tanggal,
                'pemasok_id'        => $request->pemasok_id,
                'catatan'           => $request->catatan,
                'pajak'             => $pajakPersen,
                'biaya_kirim'       => $biayaKirim,
                'diskon_nota'       => $diskonNota, 
                'total'             => $total,
                'jatuh_tempo'       => $request->jatuh_tempo,
                'status_pembayaran' => $request->status_pembayaran,
                'created_by'        => Auth::id(),
                'approved_at'       => $request->status_pembayaran === 'Lunas' ? now() : null,
            ]);

            foreach ($request->produk_id as $i => $pid) {
                $qty = (int) $request->qty[$i];
                $harga = $request->harga_beli[$i];
                $diskon = ($request->diskon[$i] ?? 0);
                $sub = ($qty * $harga - $qty *$diskon);

                $produk = MasterProduk::find($pid);

                PembelianDetail::create([
                    'pembelian_id'     => $pembelian->id,
                    'master_produk_id' => $pid,
                    'qty'              => $qty,
                    'harga_beli'       => $harga,
                    'diskon'           => $diskon,
                    'subtotal'         => $sub,
                ]);
                //  Cek hanya buat histori jika harga berbeda dengan harga default produk
                $lastHistori = HistoriHargaPembelian::where('produk_id', $produk->id)
                    ->latest('id')
                    ->first();
                if ($harga != $produk->harga_dasar && (!$lastHistori || $lastHistori->harga_baru != $harga)) {
                    $tanggalHist = $pembelian->getRawOriginal('tanggal');
                    HistoriHargaPembelian::create([
                        'produk_id' => $produk->id,
                        'pemasok_id'     => $pembelian->pemasok_id,
                        'harga_lama'       => $produk->harga_dasar,
                        'harga_baru'       => $harga,
                        'sumber'           => 'pembelian',
                        'tanggal'          => $tanggalHist,
                        'keterangan'       => 'Perubahan harga saat transaksi Faktur ' . $pembelian->no_faktur,
                    ]);
                }

                //Tambah stok
                MasterProduk::where('id', $pid)->increment('stok', $qty);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal simpan: '.$e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['pemasok', 'detail.produk'])
        -> withSum('returPembelian as total_retur', 'total')
        ->findOrFail($id);

        $produkDiretur = DB::table('retur_pembelian as rb')
        ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'rb.id')
        ->where('rb.pembelian_id', $pembelian->id)
        ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
        ->groupBy('rd.produk_id')
        ->pluck('total_qty_retur', 'rd.produk_id')
        ->map(fn($v) => $v)
        ->toArray();

        $totalRetur = ($pembelian->total_retur ?? 0);
        $totalNetto = max(0,($pembelian->total ?? 0) - $totalRetur);

        return view('purchases.purchase_inv.show', compact('pembelian','produkDiretur','totalRetur'));
    }

    public function print($id)
    {
        $profil = ProfilePerusahaan::first();
        $pembelian = Pembelian::with(['pemasok', 'detail.produk'])
        -> withSum('returPembelian as total_retur', 'total')
        ->findOrFail($id);

        $produkDiretur = DB::table('retur_pembelian as rb')
        ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'rb.id')
        ->where('rb.pembelian_id', $pembelian->id)
        ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total_qty_retur'))
        ->groupBy('rd.produk_id')
        ->pluck('total_qty_retur', 'rd.produk_id')
        ->map(fn($v) => $v)
        ->toArray();

        $totalRetur = ($pembelian->total_retur ?? 0);
        $totalNetto = max(0,($pembelian->total ?? 0) - $totalRetur);
        return view('purchases.purchase_inv.print', compact('pembelian','produkDiretur','totalRetur','profil'));

        // $pdf = PDF::loadView('penjualan.print', compact('penjualan'));
        // return $pdf->download('invoice-'.$penjualan->no_faktur.'.pdf');
    }
    public function edit($id)
    {
        $pembelian = Pembelian::with('detail', 'pemasok', 'returPembelian')->findOrFail($id);
        // dd($pembelian);
        $pemasok = Pemasok::all();
        if ($pembelian->status_pembayaran === 'Lunas') {
            return redirect()->route('pembelian.index')
                ->with('error', 'Faktur tidak dapat diedit atau dihapus karena sudah diset sebagai Lunas.');
        }
        if ($pembelian->returPembelian->count() > 0) {
            return redirect()->route('pembelian.index')
                ->with('error', 'Faktur tidak dapat diedit karena memiliki retur pembelian.');
        }
        $produk = MasterProduk::all();
        

        return view('purchases.purchase_inv.edit', compact('pembelian', 'pemasok'));
    }

public function update(Request $request, $id)
{

    $request->validate([
        'tanggal'          => ['required','date'],
        'pemasok_id'       => ['required','exists:pemasok,id'],
        'produk_id'        => ['required','array','min:1'],
        'produk_id.*'      => ['required','exists:master_produk,id'],
        'qty'              => ['required','array','min:1'],
        'qty.*'            => ['required','integer','min:1'],
        'harga_beli'       => ['required','array','min:1'],
        'harga_beli.*'     => ['required','numeric','min:0'],
        'diskon'           => ['nullable','array'],
        'diskon.*'         => ['nullable','numeric','min:0'],
        'pajak'            => ['nullable','numeric','min:0'], 
        'diskon_nota'      => ['nullable','numeric','min:0'],
        'biaya_kirim'      => ['nullable','numeric','min:0'],
        'catatan'          => ['nullable','string'],
        'jatuh_tempo'      => ['nullable','date'],
        'status_pembayaran'=> ['nullable','in:Belum Lunas,Lunas'],
        'status'           => ['nullable','in:aktif,batal'],
    ]);

    $parseMoney = function($v){
        if ($v === null) return 0;
        if (is_numeric($v)) return (float)$v;
        return (float)str_replace(['Rp','rp','.',',',' '], ['', '', '', '.', ''], $v);
    };

    DB::transaction(function() use ($request, $id, $parseMoney) {

        // Kunci header agar aman dari race condition
        $pembelian = Pembelian::lockForUpdate()->findOrFail($id);

 
        //Ambil detail lama & total qty per produk
        $oldDetails = PembelianDetail::where('pembelian_id', $pembelian->id)->get();

        $oldQtyMap = []; // [produk_id => total_qty_lama]
        foreach ($oldDetails as $d) {
            $pid = $d->master_produk_id;
            $oldQtyMap[$pid] = ($oldQtyMap[$pid] ?? 0) + $d->qty;
        }
        // Hitung total qty baru per produk dari request
        //    (antisipasi baris produk dobel)
        $newQtyMap = []; // [produk_id => total_qty_baru]
        $produkIds = $request->produk_id;
        $qtys      = $request->qty;
        $hargas    = $request->harga_beli;
        $diskons   = $request->diskon ?? [];

        foreach ($produkIds as $i => $pid) {
            $pid = $pid;
            $q   = ($qtys[$i] ?? 0);
            $newQtyMap[$pid] = ($newQtyMap[$pid] ?? 0) + $q;
        }

        // Terapkan DELTA stok per produk
        $allProductIds = array_unique(array_merge(array_keys($oldQtyMap), array_keys($newQtyMap)));

        foreach ($allProductIds as $pid) {
            $old   = ($oldQtyMap[$pid] ?? 0);
            $new   = ($newQtyMap[$pid] ?? 0);
            $delta = $new - $old; // + tambah stok, - kurangi stok

            if ($delta !== 0) {
                $produk = MasterProduk::lockForUpdate()->findOrFail($pid);

                $stokSesudah = (int)$produk->stok + (int)$delta;
                if ($stokSesudah < 0) {
                    abort(422, 'Stok produk "'.$produk->nama_produk.'" tidak mencukupi untuk koreksi. (Stok: '.$produk->stok.', delta: '.$delta.')');
                }

                if ($delta > 0) {
                    $produk->increment('stok', $delta);
                } else {
                    $produk->decrement('stok', abs($delta));
                }
            }
        }

        // Sinkronisasi detail:
        //    Hapus semua detail lama, insert ulang dari request
        PembelianDetail::where('pembelian_id', $pembelian->id)->delete();

        $detailRows = [];
        $subtotalHeader = 0;

        foreach ($produkIds as $i => $pid) {
            $pid      = $pid;
            $qty      = $qtys[$i];
            $harga    = $parseMoney($hargas[$i] ?? 0);
            $diskon   = $parseMoney($diskons[$i] ?? 0);
            

            $subtotal = max(0, ($qty * $harga - $qty * $diskon));
            $subtotalHeader += $subtotal;

            $detailRows[] = [
                'pembelian_id'     => $pembelian->id,
                'master_produk_id' => $pid,
                'qty'              => $qty,
                'harga_beli'       => $harga,
                'diskon'           => $diskon,
                'subtotal'         => $subtotal,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        if (!empty($detailRows)) {
            PembelianDetail::insert($detailRows);
        }

        $diskonNota = ($request->diskon_nota ?? 0);
        $subtotDisc = max(0, $subtotalHeader - $diskonNota);

        $pajakPersen = $parseMoney($request->pajak ?? 0);         
        $biayaKirim  = $parseMoney($request->biaya_kirim ?? 0);
        $totalPajak  = ($subtotDisc * $pajakPersen) / 100;
        $grandTotal  = $subtotDisc + $totalPajak + $biayaKirim;

        $produkHargaBaru = []; // [produk_id => harga_baru]
        foreach ($request->produk_id as $i => $pid) {
            $produkHargaBaru[(int)$pid] = (float)$parseMoney($request->harga_beli[$i] ?? 0);
        }

        // Tanggal histori
        $tanggalHist = $pembelian->getRawOriginal('tanggal') ?: \Carbon\Carbon::parse($request->tanggal)->toDateString();
        foreach ($produkHargaBaru as $pid => $hargaBaru) {
            $produk = MasterProduk::lockForUpdate()->findOrFail($pid);

            // Cek perubahan harga
            if ((float)$hargaBaru != (float)$produk->harga_dasar) {
                $lastHistori = HistoriHargaPembelian::where('produk_id', $produk->id)
                    ->latest('id')
                    ->first();

                if (!$lastHistori || (float)$lastHistori->harga_baru != (float)$hargaBaru) {
                    HistoriHargaPembelian::create([
                        'produk_id'   => $produk->id,
                        'pemasok_id'  => $pembelian->pemasok_id,
                        'harga_lama'  => $produk->harga_dasar,
                        'harga_baru'  => $hargaBaru,
                        'sumber'      => 'pembelian',
                        'tanggal'     => $tanggalHist,
                        'keterangan'  => 'Perubahan harga saat update Faktur ' . $pembelian->no_faktur,
                    ]);
                }
            }
        }

        // Update header pembelian
        $pembelian->update([
            'tanggal'          => $request->tanggal,     
            'pemasok_id'       => $request->pemasok_id,
            'catatan'          => $request->catatan,
            'pajak'            => $pajakPersen,
            'biaya_kirim'      => $biayaKirim,
            'diskon_nota'      => $diskonNota,
            'total'            => $grandTotal,
            'jatuh_tempo'      => $request->jatuh_tempo,
            'status_pembayaran'=> $request->status_pembayaran ?? $pembelian->status_pembayaran,
            'status'           => $request->status ?? $pembelian->status,
        ]);
    });

    return redirect()
        ->route('pembelian.index')
        ->with('success', 'Pembelian berhasil diperbarui. Stok telah dikoreksi berdasarkan perbedaan qty.');
}
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'tanggal' => 'required|date',
    //         'pemasok_id' => 'required',
    //         'produk_id.*' => 'required|exists:master_produk,id',
    //         'qty.*' => 'required|integer|min:1',
    //         'harga_beli.*' => 'required|numeric|min:0',
    //         'pajak' => 'nullable|numeric|min:0',
    //     ]);
    //     $pembelian = Pembelian::with('detail')->findOrFail($id);
        
    //     DB::beginTransaction();
    //     try {
    //         // Rollback stok dari detail lama (karena pembelian sebelumnya sudah menambah stok)
    //         foreach ($pembelian->detail as $d) {
    //             $produk = MasterProduk::find($d->master_produk_id);
    //             if ($produk) {
    //                 $produk->decrement('stok', $d->qty);
    //             }
    //         }

    //         //  Hapus detail lama
    //         $pembelian->detail()->delete();

    //         //  Update header faktur
    //         $pembelian->update([
    //             'tanggal'           => $request->tanggal,
    //             'pemasok_id'        => $request->pemasok_id,
    //             'catatan'           => $request->catatan,
    //             'pajak'             => $request->pajak ?? 0,
    //             'biaya_kirim'       => $request->biaya_kirim ?? 0,
    //             'no_po'             => $request->no_po,
    //             'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
    //         ]);

    //         $total = 0;

    //         foreach ($request->produk_id as $i => $produk_id) {
    //             $qty     = $request->qty[$i];
    //             $harga   = $request->harga_beli[$i]??0;
    //             $diskon  = $request->diskon[$i] ?? 0;
    //             $subtotal = ($qty * $harga) - $diskon;

    //             //  Validasi stok
    //             $produk = MasterProduk::findOrFail($produk_id);
    //             if ($produk->stok < $qty) {
    //                 DB::rollBack();
    //                 return back()->with('error', 'Stok produk "' . $produk->nama_produk . '" tidak mencukupi. Tersedia: ' . $produk->stok);
    //             }

    //             //  Simpan detail baru
    //             PembelianDetail::create([
    //                 'pembelian_id'     => $pembelian->id,
    //                 'master_produk_id' => $produk_id,
    //                 'qty'              => $qty,
    //                 'harga_beli'       => $harga,
    //                 'diskon'           => $diskon,
    //                 'subtotal'         => $subtotal,
    //             ]);

    //             $produk->increment('stok', $qty);

    //             //Cek histori harga terakhir produk ini
    //             // $lastHistori = HistoriHargaPenjualan::where('produk_id', $produk->id)
    //             //     ->where('pelanggan_id', $penjualan->pelanggan_id)
    //             //     ->latest('created_at')
    //             //     ->first();

    //             // $hargaLama = $lastHistori ? $lastHistori->harga_baru : $produk->harga_jual;
    //             //Catat hanya jika harga BERBEDA dari histori terakhir
    //             // if ($harga != $hargaLama) {
    //             //     HistoriHargaPenjualan::create([
    //             //         'produk_id' => $produk->id,
    //             //         'pelanggan_id'     => $penjualan->pelanggan_id,
    //             //         'harga_lama'       => $produk->harga_jual,
    //             //         'harga_baru'       => $harga,
    //             //         'sumber'           => 'penjualan',
    //             //         'tanggal'          => $penjualan->tanggal,
    //             //         'keterangan'       => 'Perubahan harga saat UPDATE Faktur ' . $penjualan->no_faktur,
    //             //     ]);
    //             // }

    //             $total += $subtotal;
    //         }

    //         //  Hitung ulang total faktur
    //         $pajak = $request->pajak ? ($total * $request->pajak / 100) : 0;
    //         $total += $pajak + ($request->biaya_kirim ?? 0);

    //         $pembelian->update([
    //             'total' => $total
    //         ]);

    //         DB::commit();
    //         return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Gagal update faktur: ' . $e->getMessage());
    //     }

    // }
    public function approve($id, Request $request)
    {
         $request->validate([
        'paid_date' => ['required','date_format:Y-m-d'],
        ],[
            'paid_date.required' => 'Tanggal pelunasan wajib diisi.',
        ]);
        $pembelian = Pembelian::findOrFail($id);

        $paidDate = Carbon::parse($request->paid_date)
                ->setTimeFromTimeString(now()->format('H:i:s'));
        $pembelian->update([
            'status_pembayaran' => 'Lunas',
            'approved_at' =>now('Asia/Jakarta'),
            'paid_date'         => $paidDate,
            'approved_by'        => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
        
    }
    public function unapprove($id)
    {
        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->status_pembayaran === 'Belum Lunas') {
            return back()->with('error', 'Invoice belum lunas.');
        }
        if (!$pembelian->approved_at) {
            return back()->with('error', 'Waktu persetujuan tidak ditemukan, tidak dapat dibatalkan.');
        }
        // Hitung selisih menit sejak approved
        $selisihMenit = $pembelian->approved_at->diffInMinutes(now());
        $batasMenit = 60 * 24;

        if ($selisihMenit > $batasMenit) {
            return back()->with(
                'error',
                "Batas pembatalan pelunasan sudah lewat {$batasMenit} menit, tidak bisa dibatalkan."
            );
        }

        $pembelian->update([
            'status_pembayaran' => 'Belum Lunas',
            'approved_at' => null,
            'paid_date'   => null,
        ]);

        return back()->with('success', 'Pelunasan berhasil dibatalkan.');
    }

    public function batal($id)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($id);

        // Cegah jika status pembayaran sudah Lunas
        if ($pembelian->status_pembayaran === 'Lunas') {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah dibayar.');
        }
        // Cegah jika sudah ada retur penjualan
        if ($pembelian->returPembelian && $pembelian->returPembelian->count() > 0) {
            return back()->with('error', 'Faktur tidak dapat dibatalkan karena memiliki retur pembelian.');
        }
        if ($pembelian->status === 'batal') {
            return back()->with('error', 'Faktur sudah dibatalkan sebelumnya.');
        }
 
        // Rollback stok
        foreach ($pembelian->detail as $item) {
            $produk = $item->produk;
            $produk->decrement('stok', $item->qty);
        }

        $pembelian->update([
            'status' => 'batal'
        ]);

        return redirect()->route('pembelian.index')->with('success', 'Faktur berhasil dibatalkan.');
    }
}
