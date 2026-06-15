<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mutasi Stok Produk</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2, h4 { margin: 0; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #000; padding: 5px; }
    th { background: #eee; }
    .info td, .info th { border: none; padding: 3px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
  </style>
</head>
<body>
  <h2>Mutasi Stok Produk</h2>
  <h4>
    Periode:
    {{ request('tanggal_awal') ? \Carbon\Carbon::parse(request('tanggal_awal'))->format('d/m/Y') : '-' }}
    s/d
    {{ request('tanggal_akhir') ? \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') : '-' }}
  </h4>

  <table class="info">
    <tr>
      <th style="width: 120px">Nama Produk</th>
      <td>: {{ $selectedProduk->nama_produk }}</td>
      <th style="width: 120px">Total Stok</th>
      <td>: {{ $selectedProduk->stok }}</td>
    </tr>
    <tr>
      <th>Satuan</th>
      <td>: {{ $selectedProduk->satuan->nama_satuan ?? $selectedProduk->satuan->nama ?? '-' }}</td>
      <th>Kategori</th>
      <td>: {{ $selectedProduk->kategori->nama_kategori ?? $selectedProduk->kategori->nama ?? '-' }}</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Deskripsi</th>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Sisa</th>
        <th>Penanggung Jawab</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @if(request('tanggal_awal'))
        <tr>
          <td>{{ \Carbon\Carbon::parse(request('tanggal_awal'))->format('d/m/Y') }}</td>
          <td>Saldo Sebelum Periode</td>
          <td class="text-right">0</td>
          <td class="text-right">0</td>
          <td class="text-right">{{ $saldoSebelum }}</td>
          <td>-</td>
          <td>-</td>
        </tr>
      @endif
      @forelse($mutasi as $row)
        <tr>
          <td>{{ $row->tanggal->format('d/m/Y') }}</td>
          <td>{{ $row->deskripsi }}</td>
          <td class="text-right">{{ $row->qty_masuk }}</td>
          <td class="text-right">{{ $row->qty_keluar }}</td>
          <td class="text-right">{{ $row->sisa }}</td>
          <td>{{ $row->user->name ?? '-' }}</td>
          <td>{{ $row->keterangan ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">Data mutasi tidak ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
