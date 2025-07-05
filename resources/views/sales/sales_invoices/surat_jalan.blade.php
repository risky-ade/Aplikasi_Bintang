@extends('layouts.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            {{-- <h1 class="m-0">Faktur Penjualan</h1> --}}
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active">Faktur Penjualan</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

        <!-- Main content -->
    <section class="content">
        <div class="container">
            <h2>Surat Jalan</h2>
            <p>No Surat Jalan: <strong>{{ $penjualan->no_surat_jalan }}</strong></p>
            <p>No PO: <strong>{{ $penjualan->no_po }}</strong></p>
            <p>Nama Pelanggan: {{ $penjualan->pelanggan->nama }}</p>
            <p>Tanggal: {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y') }}</p>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan->detail as $d)
                        <tr>
                            <td>{{ $d->produk->nama_produk }}</td>
                            <td>{{ $d->qty }}</td>
                            <td>{{ $d->produk->satuan->jenis_satuan}}</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <p class="mt-4">Catatan: {{ $penjualan->catatan }}</p> --}}
            <div class="row justify-content-evenly">
                <div class="col-2">
                Disiapkan :
                <br><br><br>
               ___________
                </div>
                <div class="col-2">
                Dikirim :
                <br><br><br>
               ___________
                </div>
                <div class="col-2">
                Diterima :
                <br><br><br>
               ___________
                </div>
                <div class="row justify-content-end">
                <div class="col-6">
                Catatan / Note :
                <textarea cols="30" readonly>{{ $penjualan->catatan }}</textarea>
                </div>
            </div>
        </div>
    </section>
  </div>
  @endsection