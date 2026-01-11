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
        // $users = User::with('role')->orderBy('name')->get();
        // return view('dashboard', compact('users'));
        $year = $request->get('year', now()->year);


        $totalPenjualan = DB::table('penjualan')
            // ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalPembelian = DB::table('pembelian')
            // ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalProduk = DB::table('master_produk')->count();
        $totalPelanggan = DB::table('pelanggan')->count();


        $totalNominalPenjualan = DB::table('penjualan')
            // ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');

        $totalNominalPembelian = DB::table('pembelian')
            // ->whereYear('tanggal', $year)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');


        $bulanIni = now()->month;

        $penjualanBulanIni = DB::table('penjualan')
            // ->whereYear('tanggal', $year)
            // ->whereMonth('tanggal', $bulanIni)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');

        $pembelianBulanIni = DB::table('pembelian')
            // ->whereYear('tanggal', $year)
            // ->whereMonth('tanggal', $bulanIni)
            ->where('status', '!=', 'batal')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_netto_calc');

        $penghasilanBulanIni = $penjualanBulanIni - $pembelianBulanIni;

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
            'totalNominalPembelian',
            'penghasilanBulanIni',
            'months',
            'year',
            // 'penjualanChart',
            // 'pembelianChart'
        ));
    }
}
