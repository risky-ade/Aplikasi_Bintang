<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Biaya Operasional</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2, h4 { margin: 0; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 14px; }
    th, td { border: 1px solid #000; padding: 6px; }
    th { background: #eee; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total { font-weight: bold; }
  </style>
</head>
<body>
  @php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
  @endphp
  <h2>Laporan Biaya Operasional</h2>
  <h4>
    Periode:
    {{ request('from') ? \Carbon\Carbon::parse(request('from'))->format('d/m/Y') : '-' }}
    s/d
    {{ request('to') ? \Carbon\Carbon::parse(request('to'))->format('d/m/Y') : '-' }}
  </h4>
  @if(request('kategori'))
    <h4>Kategori: {{ request('kategori') }}</h4>
  @endif

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Kategori</th>
        <th>Keterangan</th>
        <th>Nominal</th>
        <th>User</th>
      </tr>
    </thead>
    <tbody>
      @forelse($expenses as $expense)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td>{{ $expense->tanggal->format('d-m-Y') }}</td>
          <td>{{ $expense->kategori }}</td>
          <td>{{ $expense->keterangan ?? '-' }}</td>
          <td class="text-right">{{ $rupiah($expense->nominal) }}</td>
          <td>{{ $expense->user->name ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">Data biaya operasional tidak ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="text-right total">Total Biaya</td>
        <td class="text-right total">{{ $rupiah($total) }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
