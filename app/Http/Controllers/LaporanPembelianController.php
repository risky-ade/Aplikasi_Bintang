<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query()
            ->with(['pemasok'])
            ->select('pembelian.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_pembelian as rb')
                    ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'rb.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('rb.pembelian_id', 'pembelian.id');
            }, 'total_retur');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pemasok_id')) {
            $query->where('pemasok_id', $request->pemasok_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        $query->where('status', '!=', 'batal'); 

        $pembelians = $query->latest()->get();

        foreach ($pembelians as $p) {
            $pajak = ($p->pajak ?? 0);
            $ongkir = ($p->biaya_kirim ?? 0);
            $totalRetur = ($p->total_retur ?? 0);
            $den = 1 + ($pajak / 100);
            $subtotalBruto = $den != 0 ? (($p->total - $ongkir) / $den) : ($p->total - $ongkir);
            $subtotalNet = max(0, $subtotalBruto - $totalRetur);
            $pajakNet = $subtotalNet * ($pajak / 100);

            $p->total_netto_calc = $subtotalNet + $pajakNet + $ongkir;
        }
        $pemasoks = Pemasok::all();

        return view('reports/purchases_report', compact('pembelians', 'pemasoks'));
    }
    public function beliPdf(Request $request)
    {
        $query = Pembelian::query()
            ->with(['pemasok'])
            ->select('pembelian.*')
            ->selectSub(function ($sub) {
                $sub->from('retur_pembelian as r')
                    ->join('retur_pembelian_detail as rd', 'rd.retur_pembelian_id', '=', 'r.id')
                    ->selectRaw('COALESCE(SUM(rd.subtotal), 0)')
                    ->whereColumn('r.pembelian_id', 'pembelian.id');
            }, 'total_retur');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pemasok_id')) {
            $query->where('pemasok_id', $request->pemasok_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        $query->where('status', '!=', 'batal'); 

        $pembelians = $query->get();

        foreach ($pembelians as $p) {
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
        $totalRetur     = $pembelians->sum('total_retur');
        $totalNetto     = $pembelians->sum('total_netto_calc');

        $pdf = Pdf::loadView('reports.purchases_pdf', [
        'pembelians' => $pembelians,
        'totalRetur' => $totalRetur,
        'totalNetto' => $totalNetto,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('purchase_reports.pdf');
    }
}
