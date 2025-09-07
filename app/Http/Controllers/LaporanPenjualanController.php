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
        $query = Penjualan::with('pelanggan');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // $perPage = $request->get('per_page', 10);
        // $penjualans = $query->orderByDesc('tanggal')->paginate($perPage);
        // $pelanggans = Pelanggan::all();
        $penjualans = $query->get();
        $pelanggans = Pelanggan::all();

        return view('reports/sales_report', compact('penjualans', 'pelanggans'));
    }

    public function pdf(Request $request)
    {
        $query = Penjualan::with('pelanggan');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $penjualans = $query->get();
        $totalPenjualan = $penjualans->sum('total');

        $pdf = Pdf::loadView('reports.sales_pdf', [
        'penjualans' => $penjualans,
        'totalPenjualan' => $totalPenjualan
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sales_reports.pdf');
    }
}
