<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
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
            $subtotal = 0; $totalDiskon = 0;
            foreach ($request->produk_id as $i => $pid) {
                $qty = (int) $request->qty[$i];
                $harga = (float) $request->harga_beli[$i];
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
}
