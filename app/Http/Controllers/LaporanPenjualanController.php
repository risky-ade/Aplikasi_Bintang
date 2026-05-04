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

        $penjualans = $query->latest()->get();

        foreach ($penjualans as $p) {
            $pajak      = (float) ($p->pajak ?? 0);
            $ongkir     = (float) ($p->biaya_kirim ?? 0);
            $totalRetur = (float) ($p->total_retur ?? 0);

            // total = subtotal_bruto + pajak(subtotal_bruto) + ongkir
            $den = 1 + ($pajak / 100);
            $subtotalBruto = $den != 0 ? (($p->total - $ongkir) / $den) : ($p->total - $ongkir);

            // $subtotalNet = max(0, $subtotalBruto - $totalRetur);
            $subtotalNet = max(0, $subtotalBruto);
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

        foreach ($penjualans as $p) {
            $pajak      = ($p->pajak ?? 0);
            $ongkir     = ($p->biaya_kirim ?? 0);
            $totalRetur = ($p->total_retur ?? 0);
            $total      = ($p->total ?? 0);

            $den = 1 + ($pajak / 100);
            $subtotalBruto = $den != 0 ? (($total - $ongkir) / $den) : ($total - $ongkir);

            $subtotalNet = max(0, $subtotalBruto - $totalRetur);
            $pajakNet    = $subtotalNet * $pajak / 100;

            $p->total_netto_calc = $subtotalNet + $pajakNet + $ongkir;
        }
        $totalRetur     = $penjualans->sum('total_retur');
        $totalNetto     = $penjualans->sum('total_netto_calc');

        $pdf = Pdf::loadView('reports.sales_pdf', [
        'penjualans' => $penjualans,
        'totalRetur' => $totalRetur,
        'totalNetto' => $totalNetto,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sales_reports.pdf');
    }
}
