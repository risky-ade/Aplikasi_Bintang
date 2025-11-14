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
    @php
        $grandNet = 0; // akumulasi total net semua baris
    @endphp
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
        {{-- @php
            $pid = (int) $item->master_produk_id; // atau $d->produk_id
            $retQty = (int) ($produkDiretur[$pid] ?? 0);
        @endphp --}}
        @php
            $pid = (int) $item->master_produk_id; 
            $retQty = (int) ($produkDiretur[$pid] ?? 0);
            $qtyAwal = (int)($item ->qty ?? 0);
            $netQty = max(0, (int)$item->qty - $retQty);
            $biayaKirim  = (float) ($item->biaya_kirim ?? 0);
            $harga    = (float) ($item->harga_jual ?? 0);
            $discTot   = (float) ($item->diskon ?? 0);
            $discUnit = $qtyAwal > 0 ? $discTot / $qtyAwal : 0;

            $netSubtotal  = max(0, $netQty * $harga - $netQty * $discTot); //-> pakai ini jika diskon per unit(qty net * disc per unit)

            $grandNet += $netSubtotal;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>
                {{ $item->produk->nama_produk ?? '-' }}
                {{-- @if($retQty > 0)
                <br>
                <small style="font-style: italic;"> (produk diretur {{ $retQty }})</small>
                @endif --}}
            </td>
            <td style="text-align: center;">{{ $netQty }}</td>
            <td style="text-align: right;">@rupiah($item->harga_jual)</td>
            <td style="text-align: right;">@rupiah($item->diskon)</td>
            <td style="text-align: right;">@rupiah($netSubtotal)</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="row" style="margin-top: 20px;">
    @php
        $pajakPersen = (float) ($penjualan->pajak ?? 0); // misal 10 berarti 10%
        $biayaKirim  = (float) ($penjualan->biaya_kirim ?? 0);
        $totalPajak  = ($grandNet * $pajakPersen) / 100;
        $grandTotal  = $grandNet + $totalPajak + $biayaKirim;
    @endphp
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <p><strong>Terbilang:</strong></p>
                <p><em>{{ terbilang($grandTotal) }} rupiah</em></p>
                
                <p><strong>Rekening Pembayaran:</strong></p>
                <p>Bank BCA: 123-456-789 a.n. CV. Bintang Empat</p>
            </td>
            <td style="width: 30%;">
                <table class="summary-box" style="width: 100%;">
                    <tr>
                        <td><strong>Subtotal</strong></td>
                        <td style="text-align: right;">@rupiah($grandNet)</td>
                    </tr>
                    <tr>
                        <td><strong>PPN ({{ $penjualan->pajak }}%)</strong></td>
                        <td style="text-align: right;">@rupiah($totalPajak)</td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Kirim</strong></td>
                        <td style="text-align: right;">@rupiah($penjualan->biaya_kirim)</td>
                    </tr>
                    <tr>
                        <td><strong>Total Netto</strong></td>
                        <td style="text-align: right;">@rupiah($grandTotal)</td>
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
