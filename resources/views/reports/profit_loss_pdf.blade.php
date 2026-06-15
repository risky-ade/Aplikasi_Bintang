<!DOCTYPE html>
<html>
<head>
  <title>Laporan Laba Rugi</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2, h3 { margin-bottom: 6px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: left; }
    th { background-color: #eee; }
    .text-right { text-align: right; }
    .total-row { font-weight: bold; background-color: #f2f2f2; }
    .section { margin-top: 18px; }
  </style>
</head>
<body>
@php
  $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp
  <h2>Laporan Laba Rugi</h2>
  <p>Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</p>

  <div class="section">
    <h3>Ringkasan Laba Rugi</h3>
    <table>
      <tbody>
        <tr>
          <th>Penjualan Bruto</th>
          <td class="text-right">{{ $rupiah($ringkasan['penjualan_bruto']) }}</td>
        </tr>
        <tr>
          <th>Retur Penjualan</th>
          <td class="text-right">({{ $rupiah($ringkasan['retur_penjualan']) }})</td>
        </tr>
        <tr class="total-row">
          <th>Penjualan Bersih</th>
          <td class="text-right">{{ $rupiah($ringkasan['penjualan_bersih']) }}</td>
        </tr>
        <tr>
          <th>HPP Bruto</th>
          <td class="text-right">{{ $rupiah($ringkasan['hpp_bruto']) }}</td>
        </tr>
        <tr>
          <th>HPP Retur</th>
          <td class="text-right">({{ $rupiah($ringkasan['hpp_retur']) }})</td>
        </tr>
        <tr class="total-row">
          <th>HPP Bersih</th>
          <td class="text-right">{{ $rupiah($ringkasan['hpp_bersih']) }}</td>
        </tr>
        <tr class="total-row">
          <th>Laba</th>
          <td class="text-right">{{ $rupiah($ringkasan['laba_kotor']) }}</td>
        </tr>
        <tr>
          <th>Biaya Operasional</th>
          <td class="text-right">({{ $rupiah($ringkasan['biaya_operasional']) }})</td>
        </tr>
        <tr class="total-row">
          <th>Laba Setelah Operasional</th>
          <td class="text-right">{{ $rupiah($ringkasan['laba_setelah_operasional']) }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Ringkasan Pembelian dan Pembayaran</h3>
    <table>
      <tbody>
        <tr>
          <th>Pembelian Bruto</th>
          <td class="text-right">{{ $rupiah($ringkasan['pembelian_bruto']) }}</td>
        </tr>
        <tr>
          <th>Retur Pembelian</th>
          <td class="text-right">({{ $rupiah($ringkasan['retur_pembelian']) }})</td>
        </tr>
        <tr class="total-row">
          <th>Pembelian Bersih</th>
          <td class="text-right">{{ $rupiah($ringkasan['pembelian_bersih']) }}</td>
        </tr>
        <tr>
          <th>Pembayaran Diterima dari Penjualan Lunas</th>
          <td class="text-right">{{ $rupiah($ringkasan['pembayaran_masuk']) }}</td>
        </tr>
        <tr>
          <th>Pembayaran Dikirim dari Pembelian Lunas</th>
          <td class="text-right">({{ $rupiah($ringkasan['pembayaran_keluar']) }})</td>
        </tr>
        <tr class="total-row">
          <th>Pembayaran Net</th>
          <td class="text-right">{{ $rupiah($ringkasan['pembayaran_net']) }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Rincian Per Produk</h3>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Produk</th>
          <th>Qty Jual</th>
          <th>Qty Retur</th>
          <th>Penjualan Bersih</th>
          <th>HPP Bersih</th>
          <th>Laba</th>
        </tr>
      </thead>
      <tbody>
        @foreach($details as $detail)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $detail->nama_produk }}</td>
          <td class="text-right">{{ number_format($detail->qty_jual, 0, ',', '.') }}</td>
          <td class="text-right">{{ number_format($detail->qty_retur, 0, ',', '.') }}</td>
          <td class="text-right">{{ $rupiah($detail->penjualan_bersih) }}</td>
          <td class="text-right">{{ $rupiah($detail->hpp_bersih) }}</td>
          <td class="text-right">{{ $rupiah($detail->laba_kotor) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</body>
</html>
