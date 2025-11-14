<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        // $query = Penjualan::with('pelanggan');
        $query = Penjualan::query()
            ->with(['pelanggan'])
            ->select('penjualan.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_penjualan as r')
                    ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('r.penjualan_id', 'penjualan.id');
            }, 'total_retur');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        $query->where('status', '!=', 'batal'); 

        // $perPage = $request->get('per_page', 10);
        // $penjualans = $query->orderByDesc('tanggal')->paginate($perPage);
        // $pelanggans = Pelanggan::all();
        $penjualans = $query->latest()->get();

        foreach ($penjualans as $p) {
            $pajak = (float) ($p->pajak ?? 0);
            $ongkir = (float) ($p->biaya_kirim ?? 0);
            $totalRetur = (float) ($p->total_retur ?? 0);
            $den = 1 + ($pajak / 100);
            $subtotalBruto = $den != 0 ? (($p->total - $ongkir) / $den) : ($p->total - $ongkir);
            $subtotalNet = max(0, $subtotalBruto - $totalRetur);
            $pajakNet = $subtotalNet * ($pajak / 100);

            $p->total_netto_calc = $subtotalNet + $pajakNet + $ongkir;
        }
        $pelanggans = Pelanggan::all();

        return view('reports/sales_report', compact('penjualans', 'pelanggans'));
    }

    public function pdf(Request $request)
    {
        $query = Penjualan::query()
            ->with(['pelanggan'])
            ->select('penjualan.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_penjualan as r')
                    ->join('retur_penjualan_detail as rd', 'rd.retur_penjualan_id', '=', 'r.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('r.penjualan_id', 'penjualan.id');
            }, 'total_retur');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        $query->where('status', '!=', 'batal'); 

        $penjualans = $query->get();
        $totalPenjualan = $penjualans->sum('total');
        $totalRetur = $penjualans->sum('total_retur');
        $totalNetto = $penjualans->sum(function ($p) {
            $total = (float) ($p->total ?? 0);
            $retur = (float) ($p->total_retur ?? 0);
            return max(0, $total - $retur);
        });

        $pdf = Pdf::loadView('reports.sales_pdf', [
        'penjualans' => $penjualans,
        'totalPenjualan' => $totalPenjualan,
        'totalRetur' => $totalRetur,
        'totalNetto' => $totalNetto,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sales_reports.pdf');
    }
}
