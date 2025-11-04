{{-- @extends('layouts.main')
@section('content') --}}

@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #000;
    }
    .table-bordered th, .table-bordered td {
        border: 1px solid #000;
        padding: 6px;
    }
    .summary-box td {
        padding: 4px 8px;
    }
    .signature-box {
        border: 1px solid #000;
        padding: 20px;
        height: 100px;
        text-align: center;
        vertical-align: bottom;
    }
    .no-border td { border: none; }
    .center { text-align: center; }
    .ttd td { padding-top: 60px; text-align: center; }
</style>

<body>
<div style="width: 100%; margin-bottom: 20px;">
    <h2 style="text-align: center">SALES INVOICE</h2>
    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;">
                <strong>No Faktur:</strong> {{ $penjualan->no_faktur }}<br>
                <strong>Tanggal:</strong> {{ $penjualan->tanggal }}<br>
                <strong>Jatuh Tempo:</strong> {{ $penjualan->jatuh_tempo }}<br>
                <strong>No PO:</strong> {{ $penjualan->no_po }}<br>
            </td>
            <td style="width: 60%;">
                <table class="summary-box" style="width: 100%;">
                    <tr>
                        <td><strong>Pelanggan:</strong>
                        {{ $penjualan->pelanggan->nama }}<br>
                        <strong>Alamat:</strong><br>
                        {{ $penjualan->pelanggan->alamat }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
    <thead >
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Diskon</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penjualan->detail as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->produk->nama_produk }}</td>
            <td style="text-align: center;">{{ $item->qty }}</td>
            <td style="text-align: right;">@rupiah($item->harga_jual)</td>
            <td style="text-align: right;">@rupiah($item->diskon)</td>
            <td style="text-align: right;">@rupiah($item->subtotal)</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="row" style="margin-top: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <p><strong>Terbilang:</strong></p>
                <p><em>{{ terbilang($totalNetto) }} rupiah</em></p>
                
                <p><strong>Rekening Pembayaran:</strong></p>
                <p>Bank BCA: 123-456-789 a.n. CV. Bintang Empat</p>
            </td>
            <td style="width: 30%;">
                <table class="summary-box" style="width: 100%;">
                    <tr>
                        <td><strong>Subtotal</strong></td>
                        <td style="text-align: right;">@rupiah($penjualan->detail->sum('subtotal'))</td>
                    </tr>
                    <tr>
                        <td><strong>PPN ({{ $penjualan->pajak }}%)</strong></td>
                        <td style="text-align: right;">@rupiah(($penjualan->pajak/100)*$penjualan->detail->sum('subtotal'))</td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Kirim</strong></td>
                        <td style="text-align: right;">@rupiah($penjualan->biaya_kirim)</td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td style="text-align: right;">@rupiah($penjualan->total)</td>
                    </tr>
                    <tr>
                        <td><strong>Total Retur</strong></td>
                        <td style="text-align: right;">- @rupiah($totalRetur)</td>
                    </tr>
                    <tr>
                        <td><strong>Total Netto</strong></td>
                        <td style="text-align: right;">@rupiah($totalNetto)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table class="no-border ttd">
        <tr>
            <td>Disiapkan Oleh</td>
            <td>Diperiksa Oleh</td>
            <td>Dikirim Oleh</td>
            <td>Diterima Oleh</td>
        </tr>
        <tr>
            <td>________________</td>
            <td>________________</td>
            <td>________________</td>
            <td>________________</td>
        </tr>
    </table>
</div>
</body>
{{-- @endsection --}}
