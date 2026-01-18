@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
        }
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 6px;
        }
        .table-bordered th {
            background: #f2f2f2;
            text-align: center;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .summary-table td {
            padding: 5px;
        }
        .summary-table tr:last-child td {
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .terbilang-box {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 8px;
        }

        .ttd-table td {
            text-align: center;
            padding-top: 60px;
        }
    </style>
</head>
<body>

<div class="invoice-title">Sales Invoice</div>

<!-- HEADER -->
<table class="header-table">
    <tr>
        <td width="55%">
            <div class="company-name">{{ $profil->nama_perusahaan }}</div>
            {{ $profil->alamat }}<br>
            Telp: {{ $profil->telepon }}<br>
            Email: {{ $profil->email }}
        </td>
        <td width="45%">
            <table>
                <tr><td>No Faktur</td><td>: {{ $penjualan->no_faktur }}</td></tr>
                <tr><td>Tanggal</td><td>: {{ $penjualan->tanggal }}</td></tr>
                <tr><td>Jatuh Tempo</td><td>: {{ $penjualan->jatuh_tempo->format('d-m-Y') }}</td></tr>
                <tr><td>No PO</td><td>: {{ $penjualan->no_po }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<hr>

<!-- PELANGGAN -->
<table>
    <tr>
        <td width="15%"><strong>Pelanggan</strong></td>
        <td width="85%">: {{ $penjualan->pelanggan->nama }}</td>
    </tr>
    <tr>
        <td><strong>Alamat</strong></td>
        <td>: {{ $penjualan->pelanggan->alamat }}</td>
    </tr>
</table>

<br>

<!-- ITEM TABLE -->
@php $grandNet = 0; @endphp

<table class="table-bordered">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th>Produk</th>
            <th width="8%">Qty</th>
            <th width="15%">Harga</th>
            <th width="15%">Diskon</th>
            <th width="18%">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penjualan->detail as $i => $item)
        @php
            $pid = (int) $item->master_produk_id;
            $retQty = (int) ($produkDiretur[$pid] ?? 0);
            $qtyAwal = (int)($item ->qty ?? 0);
            $netQty = max(0, (int)$item->qty - $retQty);
            $biayaKirim  = (float) ($item->biaya_kirim ?? 0);
            $harga    = (float) ($item->harga_jual ?? 0);
            $discTot   = (float) ($item->diskon ?? 0);
            $discUnit = $qtyAwal > 0 ? $discTot / $qtyAwal : 0;

            $netSubtotal  = max(0, $netQty * $harga - $netQty * $discTot);
            $grandNet += $netSubtotal;
        @endphp
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $item->produk->nama_produk }}</td>
            <td class="text-center">{{ $netQty }}</td>
            <td class="text-right">@rupiah($harga)</td>
            <td class="text-right">@rupiah($discTot)</td>
            <td class="text-right">@rupiah($netSubtotal)</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

<!-- TOTAL -->
@php
    $pajakPersen = (float) ($penjualan->pajak ?? 0);
    $biayaKirim = (float) ($penjualan->biaya_kirim ?? 0);
    $totalPajak = ($grandNet * $pajakPersen) / 100;
    $grandTotal = $grandNet + $totalPajak + $biayaKirim;
@endphp

<table>
    <tr>
        <td width="55%" valign="top">
            <div class="terbilang-box">
                <strong>Terbilang:</strong><br>
                <em>{{ terbilang($grandTotal) }} rupiah</em>
            </div>

            <div class="terbilang-box">
                <strong>Pembayaran:</strong><br>
                {{ $profil->nama_bank }}<br>
                No Rek: {{ $profil->no_rekening }}
            </div>
        </td>
        <td width="45%">
            <table class="summary-table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">@rupiah($grandNet)</td>
                </tr>
                <tr>
                    <td>PPN ({{ $pajakPersen }}%)</td>
                    <td class="text-right">@rupiah($totalPajak)</td>
                </tr>
                <tr>
                    <td>Biaya Kirim</td>
                    <td class="text-right">@rupiah($biayaKirim)</td>
                </tr>
                <tr>
                    <td>Total Netto</td>
                    <td class="text-right">@rupiah($grandTotal)</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br><br>

<!-- TTD -->
<table class="ttd-table">
    <tr>
        <td>Disiapkan Oleh</td>
        <td>Diperiksa Oleh</td>
        <td>Dikirim Oleh</td>
        <td>Diterima Oleh</td>
    </tr>
    <tr>
        <td>( __________ )</td>
        <td>( __________ )</td>
        <td>( __________ )</td>
        <td>( __________ )</td>
    </tr>
</table>

</body>
</html>