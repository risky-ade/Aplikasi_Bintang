<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $penjualan->no_faktur }}</title>
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

        .catatan-box {
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
<div class="invoice-title">Surat Jalan</div>

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
                <tr><td>No Surat Jalan</td><td>: {{ $penjualan->no_surat_jalan }}</td></tr>
                <tr><td>Tanggal</td><td>: {{ $penjualan->tanggal }}</td></tr>
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
            <th width="15%">Satuan</th>
            {{-- <th width="15%">Diskon</th>
            <th width="18%">Subtotal</th> --}}
        </tr>
    </thead>
        <tbody>
            @foreach ($penjualan->detail as $i => $item)
            @php
                $pid = (int) $item->master_produk_id; 
                $retQty = (int) ($produkDiretur[$pid] ?? 0);
                $qtyAwal = (int)($item ->qty ?? 0);
                $netQty = max(0, (int)$item->qty - $retQty);
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama_produk }}</td>
                <td class="text-center">{{ $netQty }}</td>
                <td class="text-center">{{ $item->produk->satuan->jenis_satuan ?? '-'  }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
    <tr>
        <td width="55%" valign="top">
            <div class="catatan-box">
                <strong>Catatan:</strong><br>
                <em>{{ $penjualan->catatan ?? '-'  }}</em>
            </div>
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