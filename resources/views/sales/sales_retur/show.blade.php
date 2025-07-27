@extends('layouts.main')
@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Detail Retur Penjualan</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card">
        <div class="card-body">
          <h5>Informasi Umum</h5>
          <table class="table table-sm table-borderless">
            <tr><th>No Retur</th><td>{{ $retur->no_retur }}</td></tr>
            <tr><th>Tanggal Retur</th><td>{{ $retur->tanggal_retur }}</td></tr>
            <tr><th>No Faktur</th><td>{{ $retur->penjualan->no_faktur }}</td></tr>
            <tr><th>Pelanggan</th><td>{{ $retur->penjualan->pelanggan->nama ?? '-' }}</td></tr>
            <tr><th>Alasan</th><td>{{ $retur->alasan ?? '-' }}</td></tr>
          </table>

          <hr>

          <h5>Detail Produk yang Diretur</h5>
          <table class="table table-bordered">
            <thead class="bg-secondary text-white">
              <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Qty Retur</th>
                <th>Harga</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($retur->details as $i => $detail)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                <td>{{ $detail->qty_retur }}</td>
                <td>Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
              </tr>
              @endforeach
              <tr>
                <th colspan="4" class="text-right">Total</th>
                <th>Rp {{ number_format($retur->total, 0, ',', '.') }}</th>
              </tr>
            </tbody>
          </table>

          <a href="{{ route('retur-penjualan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>

    </div>
  </section>
</div>

@endsection