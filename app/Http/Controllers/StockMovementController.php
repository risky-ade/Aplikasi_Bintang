<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $this->validateFilter($request);

        $produkList = MasterProduk::with(['satuan', 'kategori'])
            ->orderBy('nama_produk')
            ->get();

        $selectedProduk = null;
        $mutasi = collect();
        $saldoSebelum = 0;

        if ($request->filled('produk_id')) {
            $selectedProduk = MasterProduk::with(['satuan', 'kategori'])->findOrFail($request->produk_id);

            $saldoSebelum = $this->getSaldoSebelum($request, $selectedProduk->id);
            $mutasi = $this->buildMutasiQuery($request, $selectedProduk->id)->get();
        }

        return view('stock_movements.index', compact('produkList', 'selectedProduk', 'mutasi', 'saldoSebelum'));
    }

    public function pdf(Request $request)
    {
        $this->validateFilter($request);

        if (! $request->filled('produk_id')) {
            return back()->with('error', 'Pilih produk terlebih dahulu sebelum export PDF.');
        }

        $selectedProduk = MasterProduk::with(['satuan', 'kategori'])->findOrFail($request->produk_id);
        $saldoSebelum = $this->getSaldoSebelum($request, $selectedProduk->id);
        $mutasi = $this->buildMutasiQuery($request, $selectedProduk->id)->get();

        Log::channel('mutasi_stok')->info('Export PDF mutasi stok produk', [
            'produk_id' => $selectedProduk->id,
            'nama_produk' => $selectedProduk->nama_produk,
            'filter' => $request->only(['produk_id', 'tanggal_awal', 'tanggal_akhir']),
            'total_data' => $mutasi->count(),
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        $pdf = Pdf::loadView('stock_movements.pdf', compact('selectedProduk', 'mutasi', 'saldoSebelum'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('mutasi-stok-produk.pdf');
    }

    private function validateFilter(Request $request): void
    {
        $request->validate([
            'produk_id' => 'nullable|exists:master_produk,id',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);
    }

    private function getSaldoSebelum(Request $request, int $produkId): int
    {
        return (int) StockMovement::where('master_produk_id', $produkId)
            ->when($request->filled('tanggal_awal'), fn ($q) => $q->whereDate('tanggal', '<', $request->tanggal_awal))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('sisa');
    }

    private function buildMutasiQuery(Request $request, int $produkId)
    {
        return StockMovement::query()
            ->with('user')
            ->where('master_produk_id', $produkId)
            ->when($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            })
            ->when($request->filled('tanggal_awal') && ! $request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->tanggal_awal);
            })
            ->when(! $request->filled('tanggal_awal') && $request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->tanggal_akhir);
            })
            ->orderBy('tanggal')
            ->orderBy('id');
    }
}
