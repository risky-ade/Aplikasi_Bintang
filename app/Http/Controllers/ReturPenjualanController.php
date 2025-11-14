<?php

namespace App\Http\Controllers;

use id;
use App\Models\Penjualan;
use App\Models\MasterProduk;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use App\Models\ReturPenjualan;
use Illuminate\Support\Facades\DB;
use App\Models\ReturPenjualanDetail;
use Illuminate\Support\Facades\Auth;

class ReturPenjualanController extends Controller
{
    public function index()
    {
        $returs = ReturPenjualan::with('penjualan')->latest()->get();
        // dd($returs);
        return view('sales.sales_retur.index', compact('returs'));
    }

    public function create()
    {
        $penjualans = Penjualan::with('pelanggan')->latest()->get();
        // $lastId = ReturPenjualan::where('tanggal_retur',now()->format('Y-m-d'))->count();
        // $no_retur = 'RTJ-' . date('Ymd') . '/' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT);
        // $tanggal_retur = now()->format('Y-m-d');
        return view('sales.sales_retur.create', compact('penjualans'));
    }

    public function getDetailPenjualan($id)
    {
        $penjualan = Penjualan::with('detail.produk')->findOrFail($id);
        // $penjualan->where('status', '!=', 'batal');
        $details = $penjualan->detail->map(function ($d) {
            $qytBaris = max(1, (int) $d->qyt);
            $diskonUnit = (float) ($d->diskon ??0)/$qytBaris; //diskon total baris->per unit
            return [
                'produk' => [
                    'id'           => $d->produk?->id,
                    'nama_produk'  => $d->produk?->nama_produk,
                ],
                'qty'         => (int) $d->qty,
                'harga_jual'  => (float) $d->harga_jual,
                'diskon'      => (int) $d->diskon,
                'diskon_unit' => (float) $diskonUnit,
                'subtotal'    => (int) $d->subtotal,
            ];
        })->values();
        return response()->json([
            'success' => true,
            'details' => $details,
        ], 200);
        // return response()->json($penjualan);
    }

    public function searchFaktur(Request $request)
    {
        $search = $request->input('q');

        $results = Penjualan::with('pelanggan')
            ->where(function($query) use ($search) {
                $query->where('no_faktur', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran','!=','Lunas')
            ->limit(20)
            ->latest()
            ->get();

        $formatted = $results->map(function($penjualan) {
            return [
                'id' => $penjualan->id,
                'text' => "{$penjualan->no_faktur} - {$penjualan->pelanggan->nama}"
            ];
        });

        return response()->json($formatted);
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'tanggal_retur' => 'required|date',
            'produk_id'      => ['required','array','min:1'],
            'produk_id.*'    => ['required','exists:master_produk,id'],
            'qty_retur'      => ['required','array','min:1'],
            'qty_retur.*' => 'nullable|integer|min:0',
            'alasan'      => 'nullable|string',
        ]);

        $last = ReturPenjualan::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $noRetur = 'RT-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $retur = ReturPenjualan::create([
                'no_retur' => $noRetur,
                'penjualan_id' => $request->penjualan_id,
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
                'total' => 0,
                'created_by' => Auth::id(),
            ]);

            
            $total = 0;
            foreach ($request->produk_id as $i => $produkId) {
                $qtyRetur = (int) ($request->qty_retur[$i] ?? 0);
                // $harga = (int) $request->harga_jual[$i];
                // $diskon = (int) $request->diskon[$i];
                if($qtyRetur <= 0)continue;
                // if ($qtyRetur > 0) {
                //     $subtotal = ($qtyRetur * $harga - $qtyRetur * $diskon);
                $pd = PenjualanDetail::where('penjualan_id', $request->penjualan_id)
                ->where('master_produk_id', $produkId)
                ->firstOrFail();

                $sudahRetur = DB::table('retur_penjualan as r')
                ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id','=', 'r.id')
                ->where('r.penjualan_id', $request->penjualan_id)
                ->where('rd.produk_id', $produkId) // (kolom di retur detail)
                ->sum('rd.qty_retur');

                $sisaBoleh = max(0, (int)$pd->qty - (int)$sudahRetur);
                if ($qtyRetur > $sisaBoleh) {
                    throw new \RuntimeException('Qty retur ' . $qtyRetur . ' melebihi sisa boleh retur ' . $sisaBoleh . ' untuk produk tersebut.');
                }

                // Hitung per unit
                $hargaUnit   = (float) ($pd->harga_jual ?? 0);
                // $qtyBaris    = max(1, (int) $pd->qty);
                // $diskonTotal = (float)($pd->diskon ?? 0); 
                // $diskonTotal = (float)($pd->diskon ?? 0); 
                // $diskonUnit  = $diskonTotal / $qtyBaris;
                $diskonUnit  = (float)($pd->diskon ?? 0);

                $netPerUnit  = max(0, $hargaUnit - $diskonUnit);
                $subRetur    = $qtyRetur * $netPerUnit;

                    ReturPenjualanDetail::create([
                        'retur_penjualan_id' => $retur->id,
                        'produk_id' => $produkId,
                        'qty_retur' => $qtyRetur,
                        'harga_jual' => $hargaUnit, //dibekukan
                        'diskon_unit' => $diskonUnit, //simpan jejak
                        'subtotal' => $subRetur,
                    ]);

                    MasterProduk::where('id', $produkId)->increment('stok', $qtyRetur);
                    $total += $subRetur;
                }
            

            $retur->update(['total' => $total]);
            DB::commit();

            return redirect()->route('retur-penjualan.index')->with('success', 'Retur berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $retur = ReturPenjualan::findOrFail($id);

        if ($retur->is_locked) {
            return redirect()->route('retur-penjualan.index')->with('error', 'Retur tidak dapat dihapus karena sudah digunakan dalam laporan.');
        }

        // Rollback stok hanya jika qty valid
        foreach ($retur->details as $detail) {
            $produk = $detail->produk;
            $qty = (int) $detail->qty_retur ?? 0;

            if ($produk && $qty > 0) {
                $produk->decrement('stok', $qty);
            }
        }

        // Hapus detail retur duluan
        $retur->details()->delete();

        // Hapus data utama
        $retur->delete();

        return redirect()->route('retur-penjualan.index')->with('success', 'Retur penjualan berhasil dihapus.');
    }

    public function show($id)
    {
        $retur = ReturPenjualan::with(['penjualan.pelanggan', 'details.produk'])->findOrFail($id);
        return view('sales.sales_retur.show', compact('retur'));
    }
}
