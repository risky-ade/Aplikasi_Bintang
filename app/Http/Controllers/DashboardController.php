<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);


        $totalPenjualan = DB::table('penjualan')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalPembelian = DB::table('pembelian')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalProduk = DB::table('master_produk')->count();
        $totalPelanggan = DB::table('pelanggan')->count();


        $totalNominalPenjualan = DB::table('penjualan')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');
        $totalPiutangPenjualan = DB::table('penjualan')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'Belum Lunas')
            ->sum('total_netto_calc');

        $totalNominalPembelian = DB::table('pembelian')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');
        
        $totalPiutangPembelian = DB::table('pembelian')
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'Belum Lunas')
            ->sum('total_netto_calc');


        $bulanIni = now()->month;

        $pembayaranMasuk = DB::table('penjualan')
            ->where('status', '!=', 'batal')
            ->whereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
            ->selectRaw('COALESCE(SUM(total), 0) as total')
            ->first();

        $returPenjualanLunas = DB::table('retur_penjualan as r')
            ->join('penjualan as p', 'p.id', '=', 'r.penjualan_id')
            ->where('p.status', '!=', 'batal')
            ->whereRaw('LOWER(p.status_pembayaran) = ?', ['lunas'])
            ->selectRaw('COALESCE(SUM(r.total), 0) as total')
            ->first();

        $pembayaranKeluar = DB::table('pembelian')
            ->where('status', '!=', 'batal')
            ->whereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
            ->selectRaw('COALESCE(SUM(total), 0) as total')
            ->first();

        $returPembelianLunas = DB::table('retur_pembelian as r')
            ->join('pembelian as p', 'p.id', '=', 'r.pembelian_id')
            ->where('p.status', '!=', 'batal')
            ->whereRaw('LOWER(p.status_pembayaran) = ?', ['lunas'])
            ->selectRaw('COALESCE(SUM(r.total), 0) as total')
            ->first();

        $totalBiayaOperasional = DB::table('operational_expenses')->sum('nominal');

        $totalPembayaranMasuk = (float) ($pembayaranMasuk->total ?? 0) - (float) ($returPenjualanLunas->total ?? 0);
        $totalPembayaranKeluar = (float) ($pembayaranKeluar->total ?? 0) - (float) ($returPembelianLunas->total ?? 0);
        $penghasilanNet = $totalPembayaranMasuk - $totalPembayaranKeluar - (float) $totalBiayaOperasional;

        $grafikPenjualan = DB::table('penjualan')
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total_netto_calc) as total')
            ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->groupBy('bulan')
            ->pluck('total','bulan');

        $grafikPembelian = DB::table('pembelian')
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total_netto_calc) as total')
            ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->groupBy('bulan')
            ->pluck('total','bulan');


        $months = collect(range(1,12))->map(function ($m) use ($grafikPenjualan, $grafikPembelian) {
            return [
                'penjualan' => $grafikPenjualan[$m] ?? 0,
                'pembelian' => $grafikPembelian[$m] ?? 0,
            ];
        });

        return view('dashboard', compact(
            'totalPenjualan',
            'totalPembelian',
            'totalProduk',
            'totalPelanggan',
            'totalNominalPenjualan',
            'totalPiutangPenjualan',
            'totalNominalPembelian',
            'totalPiutangPembelian',
            'penghasilanNet',
            'totalBiayaOperasional',
            'months',
            'year',
        ));
    }
}
