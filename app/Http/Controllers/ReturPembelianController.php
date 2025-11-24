<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\ReturPembelian;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use App\Models\ReturPembelianDetail;
use Illuminate\Support\Facades\Auth;

class ReturPembelianController extends Controller
{
    public function index()
    {
        $returns = ReturPembelian::with('pembelian.pemasok')
        ->latest()
        ->get();

        return view('purchases.purchases_retur.index', compact('returns'));
    }

    public function create()
    {
        return view('purchases.purchases_retur.create');
    }
    
    // AJAX: select2 cari faktur pembelian
    public function searchFaktur(Request $request)
    {
        $search = $request->input('q');

        $results = Pembelian::with('pemasok')
            ->where(function($q) use ($search) {
                $q->where('no_faktur', 'like', "%{$search}%")
                  ->orWhereHas('pemasok', function($qq) use ($search) {
                      $qq->where('nama', 'like', "%{$search}%");
                  });
            })
            ->where('status', '!=', 'batal')
            ->latest()
            ->limit(20)
            ->get();

        $formatted = $results->map(function($pb){
            return [
                'id'   => $pb->id,
                'text' => "{$pb->no_faktur} - {$pb->pemasok->nama}",
            ];
        });

        return response()->json($formatted);
    }

    // AJAX: get detail pembelian
    public function getDetailPembelian($id)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($id);

        $details = $pembelian->detail->map(function($d){
            // asumsikan diskon di PembelianDetail adalah diskon per-unit
            $diskonUnit = (float) ($d->diskon ?? 0);

            return [
                'produk' => [
                    'id'          => $d->produk?->id,
                    'nama_produk' => $d->produk?->nama_produk,
                ],
                'qty'          => (int) $d->qty,
                'harga_beli'   => (float) $d->harga_beli,
                'diskon_unit'  => (float) $diskonUnit,
                'subtotal'     => (float) $d->subtotal,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'details' => $details,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembelian_id'  => ['required','exists:pembelian,id'],
            'tanggal_retur' => ['required','date'],
            'produk_id'     => ['required','array','min:1'],
            'produk_id.*'   => ['required','exists:master_produk,id'],
            'qty_retur'     => ['required','array','min:1'],
            'qty_retur.*'   => ['required','integer','min:0'],
            'alasan'        => ['nullable','string'],
        ]);

        $last  = ReturPembelian::orderBy('id','desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $noRetur = 'RTP-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $retur = ReturPembelian::create([
                'no_retur'      => $noRetur,
                'pembelian_id'  => $request->pembelian_id,
                'tanggal_retur' => $request->tanggal_retur,
                'alasan'        => $request->alasan,
                'total'         => 0,
                'created_by'    => Auth::id(),
            ]);

            $total = 0;

            foreach ($request->produk_id as $i => $produkId) {
                $qtyRetur = (int) ($request->qty_retur[$i] ?? 0);
                if ($qtyRetur <= 0) continue;

                // Ambil detail pembelian asli
                $pd = PembelianDetail::where('pembelian_id', $request->pembelian_id)
                        ->where('master_produk_id', $produkId)
                        ->firstOrFail();

                $qtyBaris   = max(1, (int)$pd->qty);
                $hargaUnit  = (float) ($pd->harga_beli ?? 0);
                $diskonUnit = (float) ($pd->diskon ?? 0); // per unit

                // validasi qty retur tidak melebihi qty beli
                $sudahRetur = DB::table('retur_pembelian as r')
                    ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'r.id')
                    ->where('r.pembelian_id', $request->pembelian_id)
                    ->where('rd.produk_id', $produkId)
                    ->sum('rd.qty_retur');

                $sisaBoleh = max(0, $qtyBaris - $sudahRetur);
                if ($qtyRetur > $sisaBoleh) {
                    throw new \RuntimeException('Qty retur ' . $qtyRetur . ' melebihi sisa boleh retur ' . $sisaBoleh . ' untuk produk tersebut.');
                }

                $netPerUnit = max(0, $hargaUnit - $diskonUnit);
                $subRetur   = $qtyRetur * $netPerUnit;

                ReturPembelianDetail::create([
                    'retur_pembelian_id' => $retur->id,
                    'produk_id'          => $produkId,
                    'qty_retur'          => $qtyRetur,
                    'harga_beli'         => $hargaUnit,
                    'diskon_unit'        => $diskonUnit,
                    'subtotal'           => $subRetur,
                ]);

                // retur pembelian -> stok berkurang
                $produk = MasterProduk::lockForUpdate()->findOrFail($produkId);
                if ($produk->stok < $qtyRetur) {
                    throw new \RuntimeException('Stok produk "'.$produk->nama_produk.'" tidak mencukupi untuk retur pembelian. Stok: '.$produk->stok);
                }
                $produk->decrement('stok', $qtyRetur);

                $total += $subRetur;
            }

            $retur->update(['total' => $total]);

            DB::commit();
            return redirect()->route('retur-pembelian.index')->with('success', 'Retur pembelian berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur pembelian: '.$e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $retur = ReturPembelian::with(['pembelian.pemasok', 'details.produk'])->findOrFail($id);
        return view('purchases.purchases_retur.show', compact('retur'));
    }

    public function destroy($id)
    {
        $retur = ReturPembelian::findOrFail($id);

        // if ($retur->is_locked) {
        //     return redirect()->route('retur-penjualan.index')->with('error', 'Retur tidak dapat dihapus karena sudah digunakan dalam laporan.');
        // }

        // Rollback stok hanya jika qty valid
        foreach ($retur->details as $detail) {
            $produk = $detail->produk;
            $qty = $detail->qty_retur ?? 0;

            if ($produk && $qty > 0) {
                $produk->increment('stok', $qty);
            }
        }

        // Hapus detail retur duluan
        $retur->details()->delete();

        // Hapus data utama
        $retur->delete();

        return redirect()->route('retur-pembelian.index')->with('success', 'Retur penjualan berhasil dihapus.');
    }
}
