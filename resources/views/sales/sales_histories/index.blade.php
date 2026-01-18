@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Histori Harga Penjualan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Histori Harga Penjualan</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
      <div class="card">
        <div class="card-header">
          <form method="GET" class="row g-2 align-items-end">
              <div class="col-md-2">
                  <label>Tanggal Awal</label>
                  <input type="date" name="tanggal_awal" class="form-control"
                        value="{{ request('tanggal_awal') }}">
              </div>
              <div class="col-md-2">
                  <label>Tanggal Akhir</label>
                  <input type="date" name="tanggal_akhir" class="form-control"
                        value="{{ request('tanggal_akhir') }}">
              </div>
              <div class="col-md-3">
                  <label>Produk</label>
                  <select name="produk_id" id="produk_id" class="form-control">
                      <option value="">Semua Produk</option>
                      @foreach($produk as $p)
                          <option value="{{ $p->id }}"
                              {{ request('produk_id') == $p->id ? 'selected' : '' }}>
                              {{ $p->nama_produk }}
                          </option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-3">
                  <label>Pelanggan</label>
                  <input type="text" name="pelanggan" class="form-control" placeholder="Nama pelanggan" value="{{ request('pelanggan') }}">
              </div>
              <div class="col-md-2 d-flex">
                  <button class="btn btn-primary mx-2">
                      <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="{{ route('histori-harga-jual.index') }}" class="btn btn-secondary ">Reset</a>
              </div>
          </form>
      </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="HistoriTable" class="table table-bordered table-striped table-hover w-100">
              <thead class="bg-secondary text-white">
                  <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>Produk</th>
                      <th>Harga Lama</th>
                      <th>Harga Baru</th>
                      <th>Pelanggan</th>
                      <th>Keterangan</th>
                      <th>Waktu Input</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ($histori as $i => $row)
                  <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                      <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                      <td>@rupiah($row->harga_lama)</td>
                      <td>@rupiah($row->harga_baru)</td>
                      <td>{{ $row->pelanggan->nama ?? '-' }}</td>
                      <td>{{ $row->keterangan ?? '-' }}</td>
                      <td>{{ $row->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
</section>
</div>
<script>
$(function () {
    $('#HistoriTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true,
        autoWidth: false,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Data kosong",
            infoFiltered: "(difilter dari _MAX_ data)",
            paginate: {
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        columnDefs: [
            { targets: [0,1,2,5,7], className: 'text-nowrap' },
            { targets: [3,4], className: 'text-right' }
        ]
    });
});
</script>
<script>
$(function () {
    $('#produk_id').select2({
        placeholder: 'Pilih produk',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection