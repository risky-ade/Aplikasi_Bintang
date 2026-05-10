<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanLabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->from;
        $to = $request->to;

        $penjualan = DB::table('penjualan_detail as pd')
            ->join('penjualan as p', 'p.id', '=', 'pd.penjualan_id')
            ->join('master_produk as mp', 'mp.id', '=', 'pd.master_produk_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('p.tanggal', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('p.tanggal', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('p.tanggal', '<=', $to))
            ->selectRaw('
                COALESCE(SUM(pd.subtotal), 0) as total_penjualan,
                COALESCE(SUM(pd.qty * COALESCE(pd.harga_modal, mp.harga_dasar, 0)), 0) as total_hpp
            ')
            ->first();

        $retur = DB::table('retur_penjualan_detail as rd')
            ->join('retur_penjualan as r', 'r.id', '=', 'rd.retur_penjualan_id')
            ->join('penjualan as p', 'p.id', '=', 'r.penjualan_id')
            ->join('master_produk as mp', 'mp.id', '=', 'rd.produk_id')
            ->leftJoin('penjualan_detail as pd', 'pd.id', '=', 'rd.penjualan_detail_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('r.tanggal_retur', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('r.tanggal_retur', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('r.tanggal_retur', '<=', $to))
            ->selectRaw('
                COALESCE(SUM(rd.subtotal), 0) as total_retur,
                COALESCE(SUM(rd.qty_retur * COALESCE(pd.harga_modal, mp.harga_dasar, 0)), 0) as total_hpp_retur
            ')
            ->first();

        $totalPenjualan = (float) ($penjualan->total_penjualan ?? 0);
        $totalHpp = (float) ($penjualan->total_hpp ?? 0);
        $totalRetur = (float) ($retur->total_retur ?? 0);
        $totalHppRetur = (float) ($retur->total_hpp_retur ?? 0);
        $pembelian = DB::table('pembelian_detail as pd')
            ->join('pembelian as p', 'p.id', '=', 'pd.pembelian_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('p.tanggal', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('p.tanggal', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('p.tanggal', '<=', $to))
            ->selectRaw('COALESCE(SUM(pd.subtotal), 0) as total_pembelian')
            ->first();

        $returPembelian = DB::table('retur_pembelian_detail as rd')
            ->join('retur_pembelian as r', 'r.id', '=', 'rd.retur_pembelian_id')
            ->join('pembelian as p', 'p.id', '=', 'r.pembelian_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('r.tanggal_retur', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('r.tanggal_retur', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('r.tanggal_retur', '<=', $to))
            ->selectRaw('COALESCE(SUM(rd.subtotal), 0) as total_retur_pembelian')
            ->first();

        $pembayaranMasuk = DB::table('penjualan as p')
            ->where('p.status', '!=', 'batal')
            ->where('p.status_pembayaran', 'Lunas')
            ->when($from && $to, fn ($q) => $q->whereBetween(DB::raw('COALESCE(p.paid_date, p.tanggal)'), [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate(DB::raw('COALESCE(p.paid_date, p.tanggal)'), '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate(DB::raw('COALESCE(p.paid_date, p.tanggal)'), '<=', $to))
            ->selectRaw('COALESCE(SUM(p.total), 0) as total')
            ->first();

        $returPenjualanLunas = DB::table('retur_penjualan as r')
            ->join('penjualan as p', 'p.id', '=', 'r.penjualan_id')
            ->where('p.status', '!=', 'batal')
            ->where('p.status_pembayaran', 'Lunas')
            ->when($from && $to, fn ($q) => $q->whereBetween('r.tanggal_retur', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('r.tanggal_retur', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('r.tanggal_retur', '<=', $to))
            ->selectRaw('COALESCE(SUM(r.total), 0) as total')
            ->first();

        $pembayaranKeluar = DB::table('pembelian as p')
            ->where('p.status', '!=', 'batal')
            ->where('p.status_pembayaran', 'Lunas')
            ->when($from && $to, fn ($q) => $q->whereBetween(DB::raw('COALESCE(p.paid_date, p.tanggal)'), [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate(DB::raw('COALESCE(p.paid_date, p.tanggal)'), '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate(DB::raw('COALESCE(p.paid_date, p.tanggal)'), '<=', $to))
            ->selectRaw('COALESCE(SUM(p.total), 0) as total')
            ->first();

        $returPembelianLunas = DB::table('retur_pembelian as r')
            ->join('pembelian as p', 'p.id', '=', 'r.pembelian_id')
            ->where('p.status', '!=', 'batal')
            ->where('p.status_pembayaran', 'Lunas')
            ->when($from && $to, fn ($q) => $q->whereBetween('r.tanggal_retur', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('r.tanggal_retur', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('r.tanggal_retur', '<=', $to))
            ->selectRaw('COALESCE(SUM(r.total), 0) as total')
            ->first();

        $biayaOperasional = DB::table('operational_expenses')
            ->when($from && $to, fn ($q) => $q->whereBetween('tanggal', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->selectRaw('COALESCE(SUM(nominal), 0) as total')
            ->first();

        $totalPembelian = (float) ($pembelian->total_pembelian ?? 0);
        $totalReturPembelian = (float) ($returPembelian->total_retur_pembelian ?? 0);
        $totalPembayaranMasuk = (float) ($pembayaranMasuk->total ?? 0) - (float) ($returPenjualanLunas->total ?? 0);
        $totalPembayaranKeluar = (float) ($pembayaranKeluar->total ?? 0) - (float) ($returPembelianLunas->total ?? 0);
        $totalBiayaOperasional = (float) ($biayaOperasional->total ?? 0);

        $ringkasan = [
            'penjualan_bruto' => $totalPenjualan,
            'retur_penjualan' => $totalRetur,
            'penjualan_bersih' => $totalPenjualan - $totalRetur,
            'pembelian_bruto' => $totalPembelian,
            'retur_pembelian' => $totalReturPembelian,
            'pembelian_bersih' => $totalPembelian - $totalReturPembelian,
            'hpp_bruto' => $totalHpp,
            'hpp_retur' => $totalHppRetur,
            'hpp_bersih' => $totalHpp - $totalHppRetur,
            'laba_kotor' => ($totalPenjualan - $totalRetur) - ($totalHpp - $totalHppRetur),
            'biaya_operasional' => $totalBiayaOperasional,
            'laba_setelah_operasional' => (($totalPenjualan - $totalRetur) - ($totalHpp - $totalHppRetur)) - $totalBiayaOperasional,
            'pembayaran_masuk' => $totalPembayaranMasuk,
            'pembayaran_keluar' => $totalPembayaranKeluar,
            'pembayaran_net' => $totalPembayaranMasuk - $totalPembayaranKeluar - $totalBiayaOperasional,
        ];

        $produkPenjualan = DB::table('penjualan_detail as pd')
            ->join('penjualan as p', 'p.id', '=', 'pd.penjualan_id')
            ->join('master_produk as mp', 'mp.id', '=', 'pd.master_produk_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('p.tanggal', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('p.tanggal', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('p.tanggal', '<=', $to))
            ->groupBy('pd.master_produk_id', 'mp.nama_produk')
            ->selectRaw('
                pd.master_produk_id as produk_id,
                mp.nama_produk,
                COALESCE(SUM(pd.qty), 0) as qty_jual,
                COALESCE(SUM(pd.subtotal), 0) as penjualan,
                COALESCE(SUM(pd.qty * COALESCE(pd.harga_modal, mp.harga_dasar, 0)), 0) as hpp
            ')
            ->get()
            ->keyBy('produk_id');

        $produkRetur = DB::table('retur_penjualan_detail as rd')
            ->join('retur_penjualan as r', 'r.id', '=', 'rd.retur_penjualan_id')
            ->join('penjualan as p', 'p.id', '=', 'r.penjualan_id')
            ->join('master_produk as mp', 'mp.id', '=', 'rd.produk_id')
            ->leftJoin('penjualan_detail as pd', 'pd.id', '=', 'rd.penjualan_detail_id')
            ->where('p.status', '!=', 'batal')
            ->when($from && $to, fn ($q) => $q->whereBetween('r.tanggal_retur', [$from, $to]))
            ->when($from && ! $to, fn ($q) => $q->whereDate('r.tanggal_retur', '>=', $from))
            ->when(! $from && $to, fn ($q) => $q->whereDate('r.tanggal_retur', '<=', $to))
            ->groupBy('rd.produk_id', 'mp.nama_produk')
            ->selectRaw('
                rd.produk_id,
                mp.nama_produk,
                COALESCE(SUM(rd.qty_retur), 0) as qty_retur,
                COALESCE(SUM(rd.subtotal), 0) as retur,
                COALESCE(SUM(rd.qty_retur * COALESCE(pd.harga_modal, mp.harga_dasar, 0)), 0) as hpp_retur
            ')
            ->get()
            ->keyBy('produk_id');

        $produkIds = $produkPenjualan->keys()->merge($produkRetur->keys())->unique();

        $details = $produkIds->map(function ($produkId) use ($produkPenjualan, $produkRetur) {
            $jual = $produkPenjualan->get($produkId);
            $retur = $produkRetur->get($produkId);

            $penjualanBersih = (float) ($jual->penjualan ?? 0) - (float) ($retur->retur ?? 0);
            $hppBersih = (float) ($jual->hpp ?? 0) - (float) ($retur->hpp_retur ?? 0);

            return (object) [
                'nama_produk' => $jual->nama_produk ?? $retur->nama_produk,
                'qty_jual' => (float) ($jual->qty_jual ?? 0),
                'qty_retur' => (float) ($retur->qty_retur ?? 0),
                'penjualan_bersih' => $penjualanBersih,
                'hpp_bersih' => $hppBersih,
                'laba_kotor' => $penjualanBersih - $hppBersih,
            ];
        })->sortBy('nama_produk')->values();

        return view('reports.profit_loss', compact('ringkasan', 'details'));
    }
}
