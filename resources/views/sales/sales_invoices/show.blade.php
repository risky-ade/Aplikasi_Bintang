@extends('layouts.main')
@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Detail Faktur Penjualan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}">Penjualan</a></li>
            <li class="breadcrumb-item active">Detail Faktur</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

<section class="content">
  <div class="container-fluid">

    <div class="card">
      <div class="card-body">
        <h5><strong>Informasi Penjualan</strong></h5>
        <div class="row">
          <div class="col-md-4">
            <p><strong>No Faktur:</strong> {{ $penjualan->no_faktur }}</p>
            <p><strong>Tanggal:</strong> {{ $penjualan->tanggal }}</p>
            <p><strong>Status:</strong> 
              <span class="badge {{ $penjualan->status_pembayaran == 'Lunas' ? 'badge-success' : 'badge-warning' }}">
                {{ $penjualan->status_pembayaran }}
              </span>
            </p>
          </div>
          <div class="col-md-4">
            <p><strong>No PO:</strong> {{ $penjualan->no_po ?? '-' }}</p>
            <p><strong>Jatuh Tempo:</strong> {{ $penjualan->jatuh_tempo ?? '-' }}</p>
            <p><strong>Catatan:</strong> {{ $penjualan->catatan ?? '-' }}</p>
          </div>
          <div class="col-md-4">
            <p><strong>Pelanggan:</strong> {{ $penjualan->pelanggan->nama }}</p>
            <p><strong>Alamat:</strong> {{ $penjualan->pelanggan->alamat ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $penjualan->pelanggan->no_hp ?? '-' }}</p>
          </div>
        </div>

        <hr>

        <h5><strong>Daftar Produk</strong></h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="text-white" style="background-color: #001f3f;">
              <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @php $total = 0; $total_diskon = 0; @endphp
              @foreach ($penjualan->detail as $i => $item)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">{{ number_format($item->harga_jual, 0) }}</td>
                <td class="text-right">{{ number_format($item->diskon, 0) }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0) }}</td>
              </tr>
              @php
                $total += $item->subtotal;
                $total_diskon += $item->diskon;
              @endphp
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row justify-content-end">
          <div class="col-md-6">
            <table class="table table-sm table-borderless">
              <tr>
                <th class="text-right">Subtotal</th>
                <td class="text-right">{{ number_format($total, 0) }}</td>
              </tr>
              <tr>
                <th class="text-right">Total Diskon</th>
                <td class="text-right">{{ number_format($total_diskon, 0) }}</td>
              </tr>
              <tr>
                <th class="text-right">Pajak ({{ $penjualan->pajak }}%)</th>
                <td class="text-right">
                  @php $nilai_pajak = ($total * $penjualan->pajak) / 100; @endphp
                  {{ number_format($nilai_pajak, 0) }}
                </td>
              </tr>
              <tr>
                <th class="text-right">Biaya Kirim</th>
                <td class="text-right">{{ number_format($penjualan->biaya_kirim, 0) }}</td>
              </tr>
              <tr class="font-weight-bold">
                <th class="text-right">Total Bayar</th>
                <td class="text-right">{{ number_format($penjualan->total, 0) }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="text-right mt-3">
          <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
          <a href="{{ route('penjualan.print', $penjualan->id) }}" class="btn btn-info" target="_blank"><i class="fas fa-print"></i> Cetak</a>
        </div>

      </div>
    </div>

  </div>
</section>

</div>
@endsection