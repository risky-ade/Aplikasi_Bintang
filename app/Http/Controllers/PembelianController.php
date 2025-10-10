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

class PembelianController extends Controller
{
    public function index(Request $request)
    {
         $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);
        $query = Pembelian::with('pemasok');

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

            $pembelians = $query->latest()->paginate(15);
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
            // Hitung subtotal & total
            $subtotal = 0; 
            $totalDiskon = 0;
            foreach ($request->produk_id as $i => $pid) {
                $qty = (int) $request->qty[$i];
                $harga = $request->harga_beli[$i];
                $diskon = (float) ($request->diskon[$i] ?? 0);
                $subtotal += ($qty * $harga) - $diskon;
                $totalDiskon += $diskon;
            }
            $pajakPersen = (float) ($request->pajak ?? 0);
            $biayaKirim  = (float) ($request->biaya_kirim ?? 0);
            $totalPajak  = $subtotal * $pajakPersen / 100;
            $total       = $subtotal + $totalPajak + $biayaKirim;

            $pembelian = Pembelian::create([
                'no_faktur'         => $this->generateNoFaktur(),
                'no_po'             => $request->no_po,
                'tanggal'           => $request->tanggal,
                'pemasok_id'       => $request->pemasok_id,
                'catatan'           => $request->catatan,
                'pajak'             => $pajakPersen,
                'biaya_kirim'       => $biayaKirim,
                'total'             => $total,
                'jatuh_tempo'       => $request->jatuh_tempo,
                'status_pembayaran' => $request->status_pembayaran,
                'created_by'        => Auth::id(),
                'approved_at'       => $request->status_pembayaran === 'Lunas' ? now() : null,
            ]);

            foreach ($request->produk_id as $i => $pid) {
                $qty = (int) $request->qty[$i];
                $harga = (float) $request->harga_beli[$i];
                $diskon = (float) ($request->diskon[$i] ?? 0);
                $sub = ($qty * $harga) - $diskon;

                PembelianDetail::create([
                    'pembelian_id'     => $pembelian->id,
                    'master_produk_id' => $pid,
                    'qty'              => $qty,
                    'harga_beli'       => $harga,
                    'diskon'           => $diskon,
                    'subtotal'         => $sub,
                ]);

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
        $pembelian = Pembelian::with(['pemasok', 'detail.produk'])->findOrFail($id);
        return view('purchases.purchase_inv.show', compact('pembelian'));
    }

    public function print($id)
    {
        $pembelian = Pembelian::with(['pemasok', 'detail.produk'])->findOrFail($id);
        return view('purchases.purchase_inv.print', compact('pembelian'));

        // $pdf = PDF::loadView('penjualan.print', compact('penjualan'));
        // return $pdf->download('invoice-'.$penjualan->no_faktur.'.pdf');
    }
    public function edit($id)
    {
        $pembelian = Pembelian::with('detail', 'pemasok')->findOrFail($id);
        // dd($pembelian);
        $pemasok = Pemasok::all();
        $produk = MasterProduk::all();
        

        return view('purchases.purchase_inv.edit', compact('pembelian', 'pemasok'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pemasok_id' => 'required',
            'produk_id.*' => 'required|exists:master_produk,id',
            'qty.*' => 'required|integer|min:1',
            'harga_beli.*' => 'required|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
        ]);
        $pembelian = Pembelian::with('detail')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Rollback stok dari detail lama (karena pembelian sebelumnya sudah menambah stok)
            foreach ($pembelian->detail as $d) {
                $produk = MasterProduk::find($d->master_produk_id);
                if ($produk) {
                    $produk->decrement('stok', $d->qty);
                }
            }

            //  Hapus detail lama
            $pembelian->detail()->delete();

            //  Update header faktur
            $pembelian->update([
                'tanggal'           => $request->tanggal,
                'pemasok_id'        => $request->pemasok_id,
                'catatan'           => $request->catatan,
                'pajak'             => $request->pajak ?? 0,
                'biaya_kirim'       => $request->biaya_kirim ?? 0,
                'no_po'             => $request->no_po,
                'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
            ]);

            $total = 0;

            foreach ($request->produk_id as $i => $produk_id) {
                $qty     = $request->qty[$i];
                $harga   = $request->harga_beli[$i]??0;
                $diskon  = $request->diskon[$i] ?? 0;
                $subtotal = ($qty * $harga) - $diskon;

                //  Simpan detail baru
                PembelianDetail::create([
                    'pembelian_id'     => $pembelian->id,
                    'master_produk_id' => $produk_id,
                    'qty'              => $qty,
                    'harga_beli'       => $harga,
                    'diskon'           => $diskon,
                    'subtotal'         => $subtotal,
                ]);

                //  Kurangi stok sesuai qty baru
                $produk->increment('stok', $qty);

                //Cek histori harga terakhir produk ini
                // $lastHistori = HistoriHargaPenjualan::where('produk_id', $produk->id)
                //     ->where('pelanggan_id', $penjualan->pelanggan_id)
                //     ->latest('created_at')
                //     ->first();

                // $hargaLama = $lastHistori ? $lastHistori->harga_baru : $produk->harga_jual;
                //Catat hanya jika harga BERBEDA dari histori terakhir
                // if ($harga != $hargaLama) {
                //     HistoriHargaPenjualan::create([
                //         'produk_id' => $produk->id,
                //         'pelanggan_id'     => $penjualan->pelanggan_id,
                //         'harga_lama'       => $produk->harga_jual,
                //         'harga_baru'       => $harga,
                //         'sumber'           => 'penjualan',
                //         'tanggal'          => $penjualan->tanggal,
                //         'keterangan'       => 'Perubahan harga saat UPDATE Faktur ' . $penjualan->no_faktur,
                //     ]);
                // }

                $total += $subtotal;
            }

            //  Hitung ulang total faktur
            $pajak = $request->pajak ? ($total * $request->pajak / 100) : 0;
            $total += $pajak + ($request->biaya_kirim ?? 0);

            $pembelian->update([
                'total' => $total
            ]);

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update faktur: ' . $e->getMessage());
        }

    }
    public function approve($id, Request $request)
    {
        $request->validate([
        'approved_at' => 'required|date',
        ]);
        $pembelian = Pembelian::findOrFail($id);
        $approvedAt = Carbon::parse($request->approved_at)
                ->setTimeFromTimeString(now()->format('H:i:s'));
        $pembelian->update([
            'status_pembayaran' => 'Lunas',
            'approved_at' =>now(),
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
        // if ($pembelian->returPembelian && $pembelian->returPembelian->count() > 0) {
        //     return back()->with('error', 'Faktur tidak dapat dibatalkan karena sudah memiliki retur penjualan.');
        // }
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

        // return response()->json(['message' => 'Faktur berhasil dibatalkan.']);
        return redirect()->route('pembelian.index')->with('success', 'Faktur berhasil dibatalkan.');
    }
}
