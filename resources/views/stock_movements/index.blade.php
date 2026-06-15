@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Mutasi Stok Produk</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Mutasi Stok Produk</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <div class="card">
        <div class="card-body">
          <form method="GET" action="{{ route('stock_movements.index') }}" class="mb-3">
            <div class="row">
              <div class="col-md-4">
                <label>Nama Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                  <option value="">-- Pilih Produk --</option>
                  @foreach($produkList as $produk)
                    <option value="{{ $produk->id }}" {{ request('produk_id') == $produk->id ? 'selected' : '' }}>
                      {{ $produk->nama_produk }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label>Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
              </div>
              <div class="col-md-2">
                <label>Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('stock_movements.index') }}" class="btn btn-secondary">Reset</a>
                @if($selectedProduk)
                  <a href="{{ route('stock_movements.pdf', request()->query()) }}" class="btn btn-danger ml-2">Export PDF</a>
                @endif
              </div>
            </div>
          </form>

          @if($selectedProduk)
            <div class="text-center mb-3">
              <h4 class="mb-0"><strong>Mutasi Stok Produk</strong></h4>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                  <tr>
                    <th style="width: 120px">Nama Produk</th>
                    <td>: {{ $selectedProduk->nama_produk }}</td>
                  </tr>
                  <tr>
                    <th>Satuan</th>
                    <td>: {{ $selectedProduk->satuan->jenis_satuan ?? $selectedProduk->jenis_satuan ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Kategori</th>
                    <td>: {{ $selectedProduk->kategori->nama_kategori ?? $selectedProduk->kategori->nama ?? '-' }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                  <tr>
                    <th style="width: 120px">Total Stok</th>
                    <td>: <strong>{{ $selectedProduk->stok }}</strong></td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-sm table-striped table-hover w-100" id="MutasiTable">
                <thead class="bg-secondary text-white">
                  <tr>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Masuk</th>
                    <th class="text-center">Keluar</th>
                    <th class="text-center">Sisa</th>
                    <th class="text-center">Penanggung Jawab</th>
                    <th class="text-center">Keterangan</th>
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
            </div>
          @else
            <div class="alert alert-info mb-0">
              Pilih produk dan rentang tanggal untuk menampilkan mutasi stok.
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
</div>
<script>
$(function () {
  $('#produk_id').select2({
    placeholder: 'Pilih produk',
    allowClear: true,
    width: '100%'
  });

  $('#MutasiTable').DataTable({
    autoWidth: false,
    responsive: false,
    pageLength: 15,
    lengthMenu: [15, 25, 50, 100],
    order: [],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ baris per halaman",
      zeroRecords: "Data tidak ditemukan",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(disaring dari total _MAX_ data)",
      paginate: {
        next: "Berikutnya",
        previous: "Sebelumnya"
      }
    }
  });
});
</script>
@endsection
