<!DOCTYPE html>
<html>
<head>
  <title>Laporan Pembelian</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #eee; }
    .tfoot{font-size: 14px; font-weight: bold}
  </style>
</head>
<body>
  <h2>Laporan Pembelian</h2>
  <p>Periode: {{ request('from') }} s/d {{ request('to') }}</p>
  <table class="table table-bordered table-hover w-100 nowrap" id="laporanTable">
    <thead>
      <tr>
        <th>No</th>
        <th>No Faktur</th>
        <th>Tanggal</th>
        <th>Pemasok</th>
        <th>No PO</th>
        <th>Total Retur</th>
        <th>Total Netto</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pembelians as $i => $beli)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $beli->no_faktur }}</td>
          <td>{{ $beli->tanggal }}</td>
          <td>{{ $beli->pemasok->nama ?? '-' }}</td>
          <td>{{ $beli->no_po ?? '-' }}</td>
          <td class="text-right">Rp {{ number_format($beli->total_retur ?? 0, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format(($beli->total_netto_calc ?? $beli->total ?? 0), 0, ',', '.') }}</td>
          <td>{{ $beli->status_pembayaran }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td colspan="5" class="text-right tfoot">Total Pembelian:</td>
        <td class="text-right tfoot">Rp {{ number_format($totalRetur, 0, ',', '.') }}</td>
        <td class="text-right tfoot">Rp {{ number_format($totalNetto, 0, ',', '.') }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>