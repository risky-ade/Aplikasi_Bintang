<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\ReturPenjualan;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReturPenjualanDetail;
use App\Support\StockMovementService;
use Illuminate\Support\Facades\Auth;

class ReturPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $returs = ReturPenjualan::with('penjualan.pelanggan')
            ->when($request->filled('tanggal_awal'), function ($query) use ($request) {
                $query->whereDate('tanggal_retur', '>=', $request->tanggal_awal);
            })
            ->when($request->filled('tanggal_akhir'), function ($query) use ($request) {
                $query->whereDate('tanggal_retur', '<=', $request->tanggal_akhir);
            })
            ->when($request->filled('no_faktur'), function ($query) use ($request) {
                $query->whereHas('penjualan', function ($q) use ($request) {
                    $q->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
                });
            })
            ->when($request->filled('pelanggan'), function ($query) use ($request) {
                $query->whereHas('penjualan.pelanggan', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->pelanggan . '%');
                });
            })
            ->latest()
            ->get();

        return view('sales.sales_retur.index', compact('returs'));
    }

    public function create()
    {
        $penjualans = Penjualan::with('pelanggan')->latest()->get();
        return view('sales.sales_retur.create', compact('penjualans'));
    }

    public function getDetailPenjualan($id)
    {
        $penjualan = Penjualan::with('detail.produk.satuan')->findOrFail($id);
        $details = $penjualan->detail->map(function ($d) {
            $qtyBaris = max(1, (int) $d->qty);
            $diskonUnit = (float) ($d->diskon ?? 0);

            return [
                'produk' => [
                    'id' => $d->produk?->id,
                    'nama_produk' => $d->produk?->nama_produk,
                ],
                'qty' => (int) $d->qty,
                'satuan' => (string) ($d->produk?->satuan?->jenis_satuan ?? '-'),
                'harga_jual' => (float) $d->harga_jual,
                'diskon' => (float) ($d->diskon ?? 0),
                'diskon_unit' => $diskonUnit,
                'subtotal' => (float) $d->subtotal,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'details' => $details,
        ], 200);
    }

    public function searchFaktur(Request $request)
    {
        $search = $request->input('q');

        $results = Penjualan::with('pelanggan')
            ->where(function ($query) use ($search) {
                $query->where('no_faktur', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', '!=', 'Lunas')
            ->whereNull('approved_at')
            ->limit(20)
            ->latest()
            ->get();

        $formatted = $results->map(function ($penjualan) {
            return [
                'id' => $penjualan->id,
                'text' => "{$penjualan->no_faktur} - {$penjualan->pelanggan->nama}",
            ];
        });

        return response()->json($formatted);
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'tanggal_retur' => 'required|date',
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'exists:master_produk,id'],
            'qty_retur' => ['required', 'array', 'min:1'],
            'qty_retur.*' => 'nullable|integer|min:0',
            'alasan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::lockForUpdate()->findOrFail($request->penjualan_id);
            $this->ensurePenjualanCanChangeRetur($penjualan);

            $lastId = ReturPenjualan::whereDate('tanggal_retur', now()->toDateString())->count();
            $noRetur = 'RTJ-' . date('Ymd') . '/' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            Log::channel('retur_penjualan')->info('Mulai proses retur penjualan', [
                'no_retur' => $noRetur,
                'penjualan_id' => $penjualan->id,
                'user' => Auth::user()->name ?? null,
            ]);

            $retur = ReturPenjualan::create([
                'no_retur' => $noRetur,
                'penjualan_id' => $penjualan->id,
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
                'total' => 0,
                'created_by' => Auth::id(),
            ]);

            $total = $this->replaceDetails($retur, $penjualan, $request, false);
            $retur->update(['total' => $total]);

            DB::commit();

            Log::channel('retur_penjualan')->info('Retur berhasil', [
                'retur_id' => $retur->id,
                'total' => $total,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('retur-penjualan.index')->with('success', 'Retur berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('retur_penjualan')->error('Retur gagal', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $retur = ReturPenjualan::with(['penjualan.pelanggan', 'details.produk'])->findOrFail($id);

        if ($this->isPenjualanLocked($retur->penjualan)) {
            return redirect()->route('retur-penjualan.index')
                ->with('error', 'Retur tidak dapat diedit karena faktur penjualan sudah lunas/approve.');
        }

        $penjualanDetails = PenjualanDetail::with('produk')
            ->where('penjualan_id', $retur->penjualan_id)
            ->get();

        $returLainnya = $this->returPenjualanSebelumnya($retur->penjualan_id, $retur->id);
        $detailRetur = $retur->details->keyBy('produk_id');

        return view('sales.sales_retur.edit', compact('retur', 'penjualanDetails', 'returLainnya', 'detailRetur'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_retur' => 'required|date',
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'exists:master_produk,id'],
            'qty_retur' => ['required', 'array', 'min:1'],
            'qty_retur.*' => 'nullable|integer|min:0',
            'alasan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $retur = ReturPenjualan::with('details')->lockForUpdate()->findOrFail($id);
            $penjualan = Penjualan::lockForUpdate()->findOrFail($retur->penjualan_id);
            $this->ensurePenjualanCanChangeRetur($penjualan);

            foreach ($retur->details as $detail) {
                if ((int) $detail->qty_retur > 0) {
                    StockMovementService::record(
                        $detail->produk_id,
                        now()->toDateString(),
                        'Rollback Edit Retur Penjualan ' . $retur->no_retur,
                        0,
                        (int) $detail->qty_retur,
                        ReturPenjualan::class,
                        $retur->id,
                        'Rollback sebelum edit retur penjualan',
                        Auth::id()
                    );
                }
            }

            $retur->details()->delete();
            $retur->update([
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
            ]);

            $total = $this->replaceDetails($retur, $penjualan, $request, true);
            $retur->update(['total' => $total]);

            DB::commit();

            Log::channel('retur_penjualan')->info('Retur penjualan diedit', [
                'retur_id' => $retur->id,
                'no_retur' => $retur->no_retur,
                'total' => $total,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('retur-penjualan.index')->with('success', 'Retur penjualan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::channel('retur_penjualan')->error('Gagal edit retur penjualan', [
                'retur_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id, Request $request)
    {
        $retur = ReturPenjualan::with(['penjualan', 'details.produk'])->findOrFail($id);

        if ($this->isPenjualanLocked($retur->penjualan)) {
            return $this->returResponse($request, false, 'Retur tidak dapat dihapus karena faktur penjualan sudah lunas/approve.');
        }

        if ($retur->is_locked) {
            return $this->returResponse($request, false, 'Retur tidak dapat dihapus karena sudah digunakan dalam laporan.');
        }

        DB::beginTransaction();
        try {
            foreach ($retur->details as $detail) {
                $produk = $detail->produk;
                $qty = (int) ($detail->qty_retur ?? 0);

                if ($produk && $qty > 0) {
                    StockMovementService::record(
                        $produk->id,
                        now()->toDateString(),
                        'Hapus Retur Penjualan ' . $retur->no_retur,
                        0,
                        $qty,
                        ReturPenjualan::class,
                        $retur->id,
                        'Rollback hapus retur penjualan',
                        Auth::id()
                    );
                }
            }

            $retur->details()->delete();
            $retur->delete();

            DB::commit();

            Log::channel('retur_penjualan')->info('Retur penjualan dihapus', [
                'retur_id' => $retur->id,
                'no_retur' => $retur->no_retur,
                'user_id' => Auth::id(),
            ]);

            return $this->returResponse($request, true, 'Retur penjualan berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returResponse($request, false, $e->getMessage());
        }
    }

    public function show($id)
    {
        $retur = ReturPenjualan::with(['penjualan.pelanggan', 'details.produk'])->findOrFail($id);
        return view('sales.sales_retur.show', compact('retur'));
    }

    private function replaceDetails(ReturPenjualan $retur, Penjualan $penjualan, Request $request, bool $isUpdate): float
    {
        $details = PenjualanDetail::where('penjualan_id', $penjualan->id)
            ->get()
            ->keyBy('master_produk_id');
        $returSebelumnya = $this->returPenjualanSebelumnya($penjualan->id, $isUpdate ? $retur->id : null);
        $total = 0;
        $detailInsert = [];

        foreach ($request->produk_id as $i => $produkId) {
            $qtyRetur = (int) ($request->qty_retur[$i] ?? 0);
            if ($qtyRetur <= 0) {
                continue;
            }

            $pd = $details[$produkId] ?? null;
            if (!$pd) {
                throw new \Exception('Produk tidak ditemukan pada faktur.');
            }

            $qtyJual = (int) $pd->qty;
            $qtyReturLama = (int) ($returSebelumnya[$produkId] ?? 0);
            $sisaBoleh = max(0, $qtyJual - $qtyReturLama);

            if ($qtyRetur > $sisaBoleh) {
                throw new \Exception("Qty retur melebihi sisa ($sisaBoleh) untuk produk ID $produkId");
            }

            $harga = (float) $pd->harga_jual;
            // Diskon pada tabel penjualan_detail adalah diskon per unit
            $diskonUnit = (float) ($pd->diskon ?? 0);
            // $diskonUnit = (float) ($pd->diskon ?? 0) / max(1, $qtyJual);
            $net = max(0, $harga - $diskonUnit);
            $subtotal = $qtyRetur * $net;

            MasterProduk::lockForUpdate()->findOrFail($produkId);
            StockMovementService::record(
                $produkId,
                $request->tanggal_retur,
                'Retur Penjualan ' . $retur->no_retur,
                $qtyRetur,
                0,
                ReturPenjualan::class,
                $retur->id,
                $request->alasan,
                Auth::id()
            );

            $detailInsert[] = [
                'retur_penjualan_id' => $retur->id,
                'produk_id' => $produkId,
                'qty_retur' => $qtyRetur,
                'harga_jual' => $harga,
                'diskon_unit' => $diskonUnit,
                'subtotal' => $subtotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $total += $subtotal;
        }

        if (empty($detailInsert)) {
            throw new \Exception('Minimal isi satu qty retur.');
        }

        ReturPenjualanDetail::insert($detailInsert);

        return $total;
    }

    private function returPenjualanSebelumnya(int $penjualanId, ?int $excludeReturId = null): array
    {
        return DB::table('retur_penjualan as r')
            ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
            ->where('r.penjualan_id', $penjualanId)
            ->when($excludeReturId, fn ($query) => $query->where('r.id', '!=', $excludeReturId))
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total'))
            ->groupBy('rd.produk_id')
            ->pluck('total', 'rd.produk_id')
            ->toArray();
    }

    private function isPenjualanLocked(?Penjualan $penjualan): bool
    {
        if (!$penjualan) {
            return false;
        }

        return $penjualan->approved_at !== null || $penjualan->status_pembayaran === 'Lunas';
    }

    private function ensurePenjualanCanChangeRetur(Penjualan $penjualan): void
    {
        if ($this->isPenjualanLocked($penjualan)) {
            throw new \RuntimeException('Retur tidak dapat diubah karena faktur penjualan sudah lunas/approve.');
        }
    }

    private function returResponse(Request $request, bool $success, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message], $success ? 200 : 422);
        }

        return redirect()->route('retur-penjualan.index')->with($success ? 'success' : 'error', $message);
    }
}