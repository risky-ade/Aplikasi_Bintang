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
    <h2 style="text-align: center">PURCHASE INVOICE</h2>
    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;">
                <strong>No Faktur:</strong> {{ $pembelian->no_faktur }}<br>
                <strong>Tanggal:</strong> {{ $pembelian->tanggal }}<br>
                <strong>No PO:</strong> {{ $pembelian->no_po }}<br>
            </td>
            <td style="width: 60%;">
                <table class="summary-box" style="width: 100%;">
                    <tr>
                        <td><strong>Pemasok:</strong>
                        {{ $pembelian->pemasok->nama }}<br>
                        <strong>Alamat:</strong><br>
                        {{ $pembelian->pemasok->alamat }}
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
        @php $total = 0; $total_diskon = 0; $grandNet = 0;@endphp
        @foreach($pembelian->detail as $i => $item)
         @php
                $pid = $item->master_produk_id; 
                $retQty = ($produkDiretur[$pid] ?? 0);
                $qtyAwal = ($item ->qty ?? 0);
                $netQty = max(0, $item->qty - $retQty);
                $harga    = ($item->harga_beli ?? 0);
                $discTot   = ($item->diskon ?? 0);
                $discUnit = $qtyAwal > 0 ? $discTot / $qtyAwal : 0;

                $netSubtotal  = max(0, $netQty * $harga - $netQty * $discTot); 
                $grandNet += $netSubtotal;
              @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->produk->nama_produk }}</td>
            <td style="text-align: center;">{{ $netQty }}</td>
            <td style="text-align: right;">@rupiah($item->harga_beli)</td>
            <td style="text-align: right;">@rupiah($discTot)</td>
            <td style="text-align: right;">@rupiah($netSubtotal)</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="row" style="margin-top: 20px;">
    @php
        $diskonNota = (float) ($pembelian->diskon_nota ?? 0);
        $subtotDisc = max(0, $grandNet - $diskonNota);

        $pajakPersen = ($pembelian->pajak ?? 0);
        $biayaKirim  = ($pembelian->biaya_kirim ?? 0);
        $totalPajak  = $subtotDisc * $pajakPersen / 100;
        $total       = $subtotDisc + $totalPajak + $biayaKirim;
    @endphp
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <p><strong>Terbilang:</strong></p>
                <p><em>{{ terbilang($total) }} rupiah</em></p>
                
                {{-- <p><strong>Rekening Pembayaran:</strong></p>
                <p>Bank BCA: 123-456-789 a.n. CV. Bintang Empat</p> --}}
            </td>
            <td style="width: 30%;">
                <table class="summary-box" style="width: 100%;">
                    <tr>
                        <td><strong>Subtotal</strong></td>
                        <td style="text-align: right;">@rupiah($grandNet)</td>
                    </tr>
                    <tr>
                        <td><strong>Diskon Nota </strong></td>
                        <td style="text-align: right;">{{ rupiah($diskonNota) }}</td>
                    </tr>
                    <tr>
                        <td><strong>PPN ({{ $pembelian->pajak }}%)</strong></td>
                        <td style="text-align: right;">@rupiah($totalPajak)</td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Kirim</strong></td>
                        <td style="text-align: right;">@rupiah($biayaKirim)</td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td style="text-align: right;">@rupiah($total)</td>
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
