<!DOCTYPE html>
<html>
<head>
  <title>Laporan Penjualan</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #eee; }
    .tfoot{font-size: 14px; font-weight: bold}
  </style>
</head>
<body>
  <h2>Laporan Penjualan</h2>
  <p>Periode: {{ request('from') }} s/d {{ request('to') }}</p>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>No Faktur</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>No PO</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($penjualans as $i => $jual)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $jual->no_faktur }}</td>
          <td>{{ $jual->tanggal }}</td>
          <td>{{ $jual->pelanggan->nama ?? '-' }}</td>
          <td>{{ $jual->no_po ?? '-' }}</td>
          <td>Rp {{ number_format($jual->total) }}</td>
          <td>{{ $jual->status_pembayaran }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td colspan="5" class="text-right tfoot">Total Penjualan:</td>
        <td class="text-right tfoot">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>