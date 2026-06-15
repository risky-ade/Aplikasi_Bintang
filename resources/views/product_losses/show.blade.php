@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Detail Produk Hilang</h1>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered">
            <tr>
              <th style="width: 220px">Tanggal</th>
              <td>{{ $productLoss->tanggal->format('d-m-Y') }}</td>
            </tr>
            <tr>
              <th>Produk</th>
              <td>{{ $productLoss->produk->nama_produk ?? '-' }}</td>
            </tr>
            <tr>
              <th>Qty Hilang</th>
              <td>{{ $productLoss->qty }}</td>
            </tr>
            <tr>
              <th>Keterangan</th>
              <td>{{ $productLoss->keterangan ?? '-' }}</td>
            </tr>
            <tr>
              <th>User</th>
              <td>{{ $productLoss->user->name ?? '-' }}</td>
            </tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ route('product_losses.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
          <a href="{{ route('product_losses.edit', $productLoss->id) }}" class="btn btn-warning btn-sm">Edit</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
