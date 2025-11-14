<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $penjualan->no_faktur }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; }
        .no-border td { border: none; }
        .center { text-align: center; }
        .ttd td { padding-top: 60px; text-align: center; }
    </style>
</head>
<body>
    <h2 class="center">SURAT JALAN</h2>
    <p><strong>No Surat Jalan:</strong> {{ $penjualan->no_surat_jalan }} <br>
    <strong>Tanggal:</strong> {{ ($penjualan->tanggal) }}</p>

    <p><strong>Kepada:</strong><br>
    {{ $penjualan->pelanggan->nama }}<br>
    {{ $penjualan->pelanggan->alamat }}</p>

    <table>
        
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Satuan</th>
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
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama_produk }}</td>
                <td>{{ $netQty }}</td>
                <td>{{ $item->produk->satuan->jenis_satuan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Catatan:</strong> {{ $penjualan->catatan ?? '-' }}</p>
    <br>
    <table class="no-border ttd">
        <tr>
            <td>Disiapkan Oleh</td>
            <td>Diperiksa Oleh</td>
            <td>Dikirim Oleh</td>
            <td>Diterima Oleh</td>
        </tr>
        <tr>
            <td>__________________</td>
            <td>__________________</td>
            <td>__________________</td>
            <td>__________________</td>
        </tr>
    </table>
</body>
</html>