<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use App\Models\ProductLoss;
use App\Models\StockMovement;
use App\Support\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductLossController extends Controller
{
    public function index()
    {
        $losses = ProductLoss::with(['produk', 'user'])->latest('tanggal')->latest('id')->get();

        return view('product_losses.index', compact('losses'));
    }

    public function create()
    {
        $produk = MasterProduk::where('is_active', true)->orderBy('nama_produk')->get();

        return view('product_losses.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'master_produk_id' => 'required|array|min:1',
            'master_produk_id.*' => 'required|exists:master_produk,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['master_produk_id'] as $index => $produkId) {
                    $loss = ProductLoss::create([
                        'tanggal' => $data['tanggal'],
                        'master_produk_id' => $produkId,
                        'qty' => $data['qty'][$index],
                        'keterangan' => $data['keterangan'][$index] ?? null,
                        'created_by' => Auth::id(),
                    ]);

                    StockMovementService::record(
                        $loss->master_produk_id,
                        $loss->tanggal->format('Y-m-d'),
                        'Produk Hilang',
                        0,
                        $loss->qty,
                        ProductLoss::class,
                        $loss->id,
                        $loss->keterangan,
                        Auth::id()
                    );

                    Log::channel('produk_hilang')->info('Produk hilang berhasil dicatat', [
                        'loss_id' => $loss->id,
                        'produk_id' => $loss->master_produk_id,
                        'tanggal' => $loss->tanggal->format('Y-m-d'),
                        'qty' => $loss->qty,
                        'keterangan' => $loss->keterangan,
                        'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
                        'ip_address' => request()->ip(),
                        'waktu' => now()->toDateTimeString(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Log::channel('produk_hilang')->error('Gagal mencatat produk hilang', [
                'error' => $e->getMessage(),
                'input' => $request->except(['_token']),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('product_losses.index')
            ->with('success', 'Produk hilang berhasil dicatat dan stok telah dikurangi.');
    }

    public function show(ProductLoss $productLoss)
    {
        $productLoss->load(['produk', 'user']);

        return view('product_losses.show', compact('productLoss'));
    }

    public function edit(ProductLoss $productLoss)
    {
        $produk = MasterProduk::where('is_active', true)
            ->orWhere('id', $productLoss->master_produk_id)
            ->orderBy('nama_produk')
            ->get();

        return view('product_losses.edit', compact('productLoss', 'produk'));
    }

    public function update(Request $request, ProductLoss $productLoss)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'master_produk_id' => 'required|exists:master_produk,id',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $produkLamaId = $productLoss->master_produk_id;

        try {
            DB::transaction(function () use ($data, $productLoss, $produkLamaId) {
                $productLoss->update([
                    'tanggal' => $data['tanggal'],
                    'master_produk_id' => $data['master_produk_id'],
                    'qty' => $data['qty'],
                    'keterangan' => $data['keterangan'] ?? null,
                ]);

                $movement = StockMovement::where('reference_type', ProductLoss::class)
                    ->where('reference_id', $productLoss->id)
                    ->first();

                if ($movement) {
                    $movement->update([
                        'master_produk_id' => $productLoss->master_produk_id,
                        'tanggal' => $productLoss->tanggal->format('Y-m-d'),
                        'qty_masuk' => 0,
                        'qty_keluar' => $productLoss->qty,
                        'keterangan' => $productLoss->keterangan,
                    ]);
                } else {
                    StockMovementService::record(
                        $productLoss->master_produk_id,
                        $productLoss->tanggal->format('Y-m-d'),
                        'Produk Hilang',
                        0,
                        $productLoss->qty,
                        ProductLoss::class,
                        $productLoss->id,
                        $productLoss->keterangan,
                        Auth::id()
                    );
                }

                StockMovementService::syncProductStock($produkLamaId);

                if ($produkLamaId !== (int) $productLoss->master_produk_id) {
                    StockMovementService::syncProductStock($productLoss->master_produk_id);
                }

                Log::channel('produk_hilang')->info('Produk hilang berhasil diperbarui', [
                    'loss_id' => $productLoss->id,
                    'produk_lama_id' => $produkLamaId,
                    'produk_baru_id' => $productLoss->master_produk_id,
                    'tanggal' => $productLoss->tanggal->format('Y-m-d'),
                    'qty' => $productLoss->qty,
                    'keterangan' => $productLoss->keterangan,
                    'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
                    'ip_address' => request()->ip(),
                    'waktu' => now()->toDateTimeString(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::channel('produk_hilang')->error('Gagal memperbarui produk hilang', [
                'loss_id' => $productLoss->id,
                'error' => $e->getMessage(),
                'input' => $request->except(['_token', '_method']),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('product_losses.index')
            ->with('success', 'Produk hilang berhasil diperbarui.');
    }

    public function destroy(ProductLoss $productLoss)
    {
        try {
            DB::transaction(function () use ($productLoss) {
                $produkId = $productLoss->master_produk_id;

                StockMovement::where('reference_type', ProductLoss::class)
                    ->where('reference_id', $productLoss->id)
                    ->delete();

                $productLoss->delete();

                StockMovementService::syncProductStock($produkId);

                Log::channel('produk_hilang')->warning('Produk hilang dihapus', [
                    'loss_id' => $productLoss->id,
                    'produk_id' => $produkId,
                    'tanggal' => $productLoss->tanggal->format('Y-m-d'),
                    'qty' => $productLoss->qty,
                    'keterangan' => $productLoss->keterangan,
                    'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
                    'ip_address' => request()->ip(),
                    'waktu' => now()->toDateTimeString(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::channel('produk_hilang')->error('Gagal menghapus produk hilang', [
                'loss_id' => $productLoss->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);

            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('product_losses.index')
            ->with('success', 'Produk hilang berhasil dihapus dan stok telah dikoreksi.');
    }
}
