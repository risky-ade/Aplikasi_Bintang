@extends('layouts.main')
@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp
<style>
  .nowrap th, .nowrap td { white-space: nowrap; }
  .summary-card .label { color: #6c757d; font-size: 13px; margin-bottom: 4px; }
  .summary-card .value { font-size: 20px; font-weight: 700; margin-bottom: 0; }
</style>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Laporan Laba Kotor</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Laporan Laba Kotor</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="card-body">
      <form method="GET" action="{{ route('profit_loss.index') }}" class="mb-3">
        <div class="row">
          <div class="col-md-2">
            <label>Dari Tanggal</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
          </div>
          <div class="col-md-2">
            <label>Sampai Tanggal</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('profit_loss.index') }}" class="btn btn-secondary">Reset</a>
          </div>
        </div>
      </form>

      <div class="row">
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Penjualan Bersih</div>
              <p class="value">{{ $rupiah($ringkasan['penjualan_bersih']) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">HPP Bersih</div>
              <p class="value">{{ $rupiah($ringkasan['hpp_bersih']) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Laba</div>
              <p class="value {{ $ringkasan['laba_kotor'] < 0 ? 'text-danger' : 'text-success' }}">
                {{ $rupiah($ringkasan['laba_kotor']) }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Biaya Operasional</div>
              <p class="value text-danger">{{ $rupiah($ringkasan['biaya_operasional']) }}</p>
              {{-- <div class="label">Margin Kotor</div>
              <p class="value">
                {{ $ringkasan['penjualan_bersih'] > 0 ? number_format(($ringkasan['laba_kotor'] / $ringkasan['penjualan_bersih']) * 100, 2, ',', '.') : '0,00' }}%
              </p> --}}
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Pembelian Bersih</div>
              <p class="value">{{ $rupiah($ringkasan['pembelian_bersih']) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Pembayaran Masuk</div>
              <p class="value text-success">{{ $rupiah($ringkasan['pembayaran_masuk']) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Pembayaran Keluar</div>
              <p class="value text-danger">{{ $rupiah($ringkasan['pembayaran_keluar']) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card summary-card">
            <div class="card-body">
              <div class="label">Pembayaran Net</div>
              <p class="value {{ $ringkasan['pembayaran_net'] < 0 ? 'text-danger' : 'text-success' }}">
                {{ $rupiah($ringkasan['pembayaran_net']) }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <strong>Ringkasan</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Penjualan Bruto</th>
                  <td class="text-right">{{ $rupiah($ringkasan['penjualan_bruto']) }}</td>
                </tr>
                <tr>
                  <th>Retur Penjualan</th>
                  <td class="text-right">({{ $rupiah($ringkasan['retur_penjualan']) }})</td>
                </tr>
                <tr>
                  <th>Penjualan Bersih</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['penjualan_bersih']) }}</strong></td>
                </tr>
                <tr>
                  <th>Pembelian Bruto</th>
                  <td class="text-right">{{ $rupiah($ringkasan['pembelian_bruto']) }}</td>
                </tr>
                <tr>
                  <th>Retur Pembelian</th>
                  <td class="text-right">({{ $rupiah($ringkasan['retur_pembelian']) }})</td>
                </tr>
                <tr>
                  <th>Pembelian Bersih</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['pembelian_bersih']) }}</strong></td>
                </tr>
                <tr>
                  <th>HPP Bruto</th>
                  <td class="text-right">{{ $rupiah($ringkasan['hpp_bruto']) }}</td>
                </tr>
                <tr>
                  <th>HPP Retur</th>
                  <td class="text-right">({{ $rupiah($ringkasan['hpp_retur']) }})</td>
                </tr>
                <tr>
                  <th>HPP Bersih</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['hpp_bersih']) }}</strong></td>
                </tr>
                <tr class="{{ $ringkasan['laba_kotor'] < 0 ? 'table-danger' : 'table-success' }}">
                  <th>Laba</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['laba_kotor']) }}</strong></td>
                </tr>
                {{-- <tr>
                  <th>Biaya Operasional</th>
                  <td class="text-right">({{ $rupiah($ringkasan['biaya_operasional']) }})</td>
                </tr>
                <tr class="{{ $ringkasan['laba_setelah_operasional'] < 0 ? 'table-danger' : 'table-success' }}">
                  <th>Laba Setelah Operasional</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['laba_setelah_operasional']) }}</strong></td>
                </tr> --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <strong>Ringkasan Pembayaran</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Pembayaran Diterima dari Penjualan Lunas</th>
                  <td class="text-right text-success"><strong>{{ $rupiah($ringkasan['pembayaran_masuk']) }}</strong></td>
                </tr>
                <tr>
                  <th>Pembayaran Dikirim dari Pembelian Lunas</th>
                  <td class="text-right text-danger">({{ $rupiah($ringkasan['pembayaran_keluar']) }})</td>
                </tr>
                <tr>
                  <th>Biaya Operasional</th>
                  <td class="text-right text-danger">({{ $rupiah($ringkasan['biaya_operasional']) }})</td>
                </tr>
                <tr class="{{ $ringkasan['pembayaran_net'] < 0 ? 'table-danger' : 'table-success' }}">
                  <th>Pembayaran Net</th>
                  <td class="text-right"><strong>{{ $rupiah($ringkasan['pembayaran_net']) }}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>

      <div class="card">
        <div class="card-header">
          <strong>Rincian Per Produk</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover w-100 nowrap" id="labaKotorTable">
              <thead class="bg-secondary text-white">
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
                  <td>{{ number_format($detail->qty_jual, 0, ',', '.') }}</td>
                  <td>{{ number_format($detail->qty_retur, 0, ',', '.') }}</td>
                  <td>{{ $rupiah($detail->penjualan_bersih) }}</td>
                  <td>{{ $rupiah($detail->hpp_bersih) }}</td>
                  <td class="{{ $detail->laba_kotor < 0 ? 'text-danger' : 'text-success' }}">
                    {{ $rupiah($detail->laba_kotor) }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function() {
    $('#labaKotorTable').DataTable({
      autoWidth: false,
      responsive: false,
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,2,3,4,5,6], className: 'text-nowrap' },
        { targets: [1], width: '260px' }
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ baris per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari total _MAX_ data)",
        paginate: { next: "Berikutnya", previous: "Sebelumnya" }
      }
    });
  });
</script>
@endsection
