<?php

namespace App\Http\Controllers;

use id;
use App\Models\Penjualan;
use App\Models\MasterProduk;
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
        return response()->json($penjualan);
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
            'qty_retur.*' => 'nullable|integer|min:0'
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
                $qtyRetur = (int) $request->qty_retur[$i];
                $harga = (int) $request->harga_jual[$i];

                if ($qtyRetur > 0) {
                    $subtotal = $qtyRetur * $harga;

                    ReturPenjualanDetail::create([
                        'retur_penjualan_id' => $retur->id,
                        'produk_id' => $produkId,
                        'qty_retur' => $qtyRetur,
                        'harga_jual' => $harga,
                        'subtotal' => $subtotal
                    ]);

                    MasterProduk::where('id', $produkId)->increment('stok', $qtyRetur);
                    $total += $subtotal;
                }
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
