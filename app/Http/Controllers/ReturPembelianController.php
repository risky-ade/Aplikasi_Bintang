<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\ReturPembelian;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReturPembelianDetail;
use App\Support\StockMovementService;
use Illuminate\Support\Facades\Auth;

class ReturPembelianController extends Controller
{
    public function index(Request $request)
    {
        $returns = ReturPembelian::with('pembelian.pemasok')
            ->when($request->filled('tanggal_awal'), function ($query) use ($request) {
                $query->whereDate('tanggal_retur', '>=', $request->tanggal_awal);
            })
            ->when($request->filled('tanggal_akhir'), function ($query) use ($request) {
                $query->whereDate('tanggal_retur', '<=', $request->tanggal_akhir);
            })
            ->when($request->filled('no_faktur'), function ($query) use ($request) {
                $query->whereHas('pembelian', function ($q) use ($request) {
                    $q->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
                });
            })
            ->when($request->filled('pemasok'), function ($query) use ($request) {
                $query->whereHas('pembelian.pemasok', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->pemasok . '%');
                });
            })
            ->latest()
            ->get();

        return view('purchases.purchases_retur.index', compact('returns'));
    }

    public function create()
    {
        return view('purchases.purchases_retur.create');
    }

    public function searchFaktur(Request $request)
    {
        $search = $request->input('q');

        $results = Pembelian::with('pemasok')
            ->where(function ($q) use ($search) {
                $q->where('no_faktur', 'like', "%{$search}%")
                    ->orWhereHas('pemasok', function ($qq) use ($search) {
                        $qq->where('nama', 'like', "%{$search}%");
                    });
            })
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', '!=', 'Lunas')
            ->whereNull('approved_at')
            ->latest()
            ->limit(20)
            ->get();

        $formatted = $results->map(function ($pb) {
            return [
                'id' => $pb->id,
                'text' => "{$pb->no_faktur} - {$pb->pemasok->nama}",
            ];
        });

        return response()->json($formatted);
    }

    public function getDetailPembelian($id)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($id);

        $details = $pembelian->detail->map(function ($d) {
            $diskonUnit = (float) ($d->diskon ?? 0);

            return [
                'produk' => [
                    'id' => $d->produk?->id,
                    'nama_produk' => $d->produk?->nama_produk,
                ],
                'qty' => (int) $d->qty,
                'harga_beli' => (float) $d->harga_beli,
                'diskon_unit' => $diskonUnit,
                'subtotal' => (float) $d->subtotal,
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
            'pembelian_id' => ['required', 'exists:pembelian,id'],
            'tanggal_retur' => ['required', 'date'],
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'exists:master_produk,id'],
            'qty_retur' => ['required', 'array', 'min:1'],
            'qty_retur.*' => ['required', 'integer', 'min:0'],
            'alasan' => ['nullable', 'string'],
        ]);

        $last = ReturPembelian::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $noRetur = 'RTP-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::lockForUpdate()->findOrFail($request->pembelian_id);
            $this->ensurePembelianCanChangeRetur($pembelian);

            Log::channel('retur_pembelian')->info('Mulai proses simpan retur pembelian', [
                'no_retur' => $noRetur,
                'user_id' => Auth::id(),
            ]);

            $retur = ReturPembelian::create([
                'no_retur' => $noRetur,
                'pembelian_id' => $pembelian->id,
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
                'total' => 0,
                'created_by' => Auth::id(),
            ]);

            $total = $this->replaceDetails($retur, $pembelian, $request, false);
            $retur->update(['total' => $total]);

            DB::commit();
            Log::channel('retur_pembelian')->info('Retur pembelian berhasil disimpan', [
                'retur_id' => $retur->id,
                'no_retur' => $noRetur,
                'total' => $retur->total,
            ]);

            return redirect()->route('retur-pembelian.index')->with('success', 'Retur pembelian berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::channel('retur_pembelian')->error('Gagal simpan retur pembelian', [
                'no_retur' => $noRetur,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menyimpan retur pembelian: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $retur = ReturPembelian::with(['pembelian.pemasok', 'details.produk'])->findOrFail($id);
        return view('purchases.purchases_retur.show', compact('retur'));
    }

    public function edit($id)
    {
        $retur = ReturPembelian::with(['pembelian.pemasok', 'details.produk'])->findOrFail($id);

        if ($this->isPembelianLocked($retur->pembelian)) {
            return redirect()->route('retur-pembelian.index')
                ->with('error', 'Retur tidak dapat diedit karena faktur pembelian sudah lunas/approve.');
        }

        $pembelianDetails = PembelianDetail::with('produk')
            ->where('pembelian_id', $retur->pembelian_id)
            ->get();

        $returLainnya = $this->returPembelianSebelumnya($retur->pembelian_id, $retur->id);
        $detailRetur = $retur->details->keyBy('produk_id');

        return view('purchases.purchases_retur.edit', compact('retur', 'pembelianDetails', 'returLainnya', 'detailRetur'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_retur' => ['required', 'date'],
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'exists:master_produk,id'],
            'qty_retur' => ['required', 'array', 'min:1'],
            'qty_retur.*' => ['required', 'integer', 'min:0'],
            'alasan' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $retur = ReturPembelian::with('details')->lockForUpdate()->findOrFail($id);
            $pembelian = Pembelian::lockForUpdate()->findOrFail($retur->pembelian_id);
            $this->ensurePembelianCanChangeRetur($pembelian);

            foreach ($retur->details as $detail) {
                if ((int) $detail->qty_retur > 0) {
                    StockMovementService::record(
                        $detail->produk_id,
                        now()->toDateString(),
                        'Rollback Edit Retur Pembelian ' . $retur->no_retur,
                        (int) $detail->qty_retur,
                        0,
                        ReturPembelian::class,
                        $retur->id,
                        'Rollback sebelum edit retur pembelian',
                        Auth::id()
                    );
                }
            }

            $retur->details()->delete();
            $retur->update([
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
            ]);

            $total = $this->replaceDetails($retur, $pembelian, $request, true);
            $retur->update(['total' => $total]);

            DB::commit();

            Log::channel('retur_pembelian')->info('Retur pembelian diedit', [
                'retur_id' => $retur->id,
                'no_retur' => $retur->no_retur,
                'total' => $total,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('retur-pembelian.index')->with('success', 'Retur pembelian berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::channel('retur_pembelian')->error('Gagal edit retur pembelian', [
                'retur_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal memperbarui retur pembelian: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id, Request $request)
    {
        $retur = ReturPembelian::with(['pembelian', 'details.produk'])->findOrFail($id);

        if ($this->isPembelianLocked($retur->pembelian)) {
            return $this->returResponse($request, false, 'Retur tidak dapat dihapus karena faktur pembelian sudah lunas/approve.');
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
                        'Hapus Retur Pembelian ' . $retur->no_retur,
                        $qty,
                        0,
                        ReturPembelian::class,
                        $retur->id,
                        'Rollback hapus retur pembelian',
                        Auth::id()
                    );
                }
            }

            $retur->details()->delete();
            $retur->delete();

            DB::commit();

            Log::channel('retur_pembelian')->info('Retur pembelian dihapus', [
                'retur_id' => $retur->id,
                'no_retur' => $retur->no_retur,
                'user_id' => Auth::id(),
            ]);

            return $this->returResponse($request, true, 'Retur pembelian berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returResponse($request, false, $e->getMessage());
        }
    }

    private function replaceDetails(ReturPembelian $retur, Pembelian $pembelian, Request $request, bool $isUpdate): float
    {
        $details = PembelianDetail::where('pembelian_id', $pembelian->id)
            ->get()
            ->keyBy('master_produk_id');
        $returSebelumnya = $this->returPembelianSebelumnya($pembelian->id, $isUpdate ? $retur->id : null);
        $total = 0;

        foreach ($request->produk_id as $i => $produkId) {
            $qtyRetur = (int) ($request->qty_retur[$i] ?? 0);
            if ($qtyRetur <= 0) {
                continue;
            }

            $pd = $details[$produkId] ?? null;
            if (!$pd) {
                throw new \RuntimeException('Produk tidak ditemukan pada faktur pembelian.');
            }

            $qtyBaris = (int) $pd->qty;
            $sudahRetur = (int) ($returSebelumnya[$produkId] ?? 0);
            $sisaBoleh = max(0, $qtyBaris - $sudahRetur);

            if ($qtyRetur > $sisaBoleh) {
                throw new \RuntimeException('Qty retur ' . $qtyRetur . ' melebihi sisa boleh retur ' . $sisaBoleh . ' untuk produk tersebut.');
            }

            $hargaUnit = (float) ($pd->harga_beli ?? 0);
            $diskonUnit = (float) ($pd->diskon ?? 0);
            $netPerUnit = max(0, $hargaUnit - $diskonUnit);
            $subRetur = $qtyRetur * $netPerUnit;

            $produk = MasterProduk::lockForUpdate()->findOrFail($produkId);
            if ($produk->stok < $qtyRetur) {
                throw new \RuntimeException('Stok produk "' . $produk->nama_produk . '" tidak mencukupi untuk retur pembelian. Stok: ' . $produk->stok);
            }

            ReturPembelianDetail::create([
                'retur_pembelian_id' => $retur->id,
                'produk_id' => $produkId,
                'qty_retur' => $qtyRetur,
                'harga_beli' => $hargaUnit,
                'diskon_unit' => $diskonUnit,
                'subtotal' => $subRetur,
            ]);

            StockMovementService::record(
                $produk->id,
                $request->tanggal_retur,
                'Retur Pembelian ' . $retur->no_retur,
                0,
                $qtyRetur,
                ReturPembelian::class,
                $retur->id,
                $request->alasan,
                Auth::id()
            );

            $total += $subRetur;
        }

        if ($total <= 0) {
            throw new \RuntimeException('Minimal isi satu qty retur.');
        }

        return $total;
    }

    private function returPembelianSebelumnya(int $pembelianId, ?int $excludeReturId = null): array
    {
        return DB::table('retur_pembelian as r')
            ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'r.id')
            ->where('r.pembelian_id', $pembelianId)
            ->when($excludeReturId, fn ($query) => $query->where('r.id', '!=', $excludeReturId))
            ->select('rd.produk_id', DB::raw('SUM(rd.qty_retur) as total'))
            ->groupBy('rd.produk_id')
            ->pluck('total', 'rd.produk_id')
            ->toArray();
    }

    private function isPembelianLocked(?Pembelian $pembelian): bool
    {
        if (!$pembelian) {
            return false;
        }

        return $pembelian->approved_at !== null || $pembelian->status_pembayaran === 'Lunas';
    }

    private function ensurePembelianCanChangeRetur(Pembelian $pembelian): void
    {
        if ($this->isPembelianLocked($pembelian)) {
            throw new \RuntimeException('Retur tidak dapat diubah karena faktur pembelian sudah lunas/approve.');
        }
    }

    private function returResponse(Request $request, bool $success, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message], $success ? 200 : 422);
        }

        return redirect()->route('retur-pembelian.index')->with($success ? 'success' : 'error', $message);
    }
}