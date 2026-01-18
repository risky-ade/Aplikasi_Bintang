@extends('layouts.main')
@section('content')
<style>
  .dropdown-menu {
    max-width: 220px;
    word-wrap: break-word;
  }
    .nowrap th, .nowrap td { white-space: nowrap; }
</style>
<div class="content-wrapper">
  <div class="content-header">
      <div class="container-fluid">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1 class="m-0">Daftar Produk</h1>
              </div>
              <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item active">Master Produk</li>
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
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/master_produk/create">
                        <button class="btn btn-primary me-md-2" type="button"><i class="fas fa-solid fa-plus"></i>Tambah Data</button>
                    </a>
                </div>
            </div>
            <div class="card-body">
            <form method="GET" class="mb-3">
              <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="nama" class="form-control" placeholder="Cari nama produk..." value="{{ request('nama') }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori_id" class="form-control">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}"
                                {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button class="btn btn-primary w-100 mx-1">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('master_produk.index') }}"
                      class="btn btn-secondary w-100">
                        Reset
                    </a>
                </div>
              </div>
          </form>
            <div class="table-responsive">
                <table id="ProductTable" class="table table-bordered table-striped table-hover w-100 nowrap">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            {{-- <th>Deskripsi</th> --}}
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Harga Dasar</th>
                            <th>Stok</th>
                            <th>Status Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($masterProduk as $row)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>
                                @if($row->gambar)
                                    <img src="{{ asset('storage/'.$row->gambar) }}" width="60" alt="gambar">
                                @else
                                    -
                                @endif
                            </td>
                              <td>{{ $row->nama_produk }}</td>
                              {{-- <td>{{ $row->deskripsi }}</td> --}}
                              <td>{{ $row->kategori->nama_kategori ??'Null' }}</td>
                              <td>{{ $row->satuan->jenis_satuan ??'Null' }}</td>
                              <td>{{ number_format($row->harga_dasar, 0, ',','.') }}</td>
                              <td>{{ $row->stok }}</td>
                              <td>
                                  @if ($row->stok <= $row->stok_minimal)
                                      <span class="badge badge-danger ml-2">Stok Minim!</span>
                                  @endif
                              </td>
                              <td class="text-center">
                                  @if($row->is_active)
                                      <span class="badge bg-success">Aktif</span>
                                  @else
                                      <span class="badge bg-secondary">Nonaktif</span>
                                  @endif
                              </td>
                              <td>
                                <div class="dropdown ">
                                  <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-bars"></i>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ url('/master_produk/'.$row->id.'/edit') }}" class="dropdown-item text-info"
                                      ><i class="fa fa-edit"> Edit</i> </a>
                                    <button class="dropdown-item text-danger btn-delete" data-id="{{ $row->id }}" data-nama="{{ $row->nama_produk }}">
                                        <i class="fas fa-trash-alt"> Hapus</i>
                                    </button>
                                    <button type="button"class="dropdown-item btn {{ $row->is_active ? 'text-warning' : 'text-success' }} btn-toggle" data-id="{{ $row->id }}">
                                    <i class="fas fa-times-circle"></i> {{ $row->is_active ? 'Nonaktif' : 'Aktif' }} 
                                    </button>      
                                  </div>
                                </div>
                              </td>
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

</div>
  <script>
  $(document).ready(function() {
    $('#ProductTable').DataTable({
      autoWidth: false,    
      responsive: false,    
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,1,2,4,5,6,7,8], className: 'text-nowrap' },
        { targets: [3], width: '220px' }
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ baris per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari total _MAX_ data)",
        paginate: { next: "Berikutnya", previous: "Sebelumnya" }
      },
    });
  });
</script>
<script>
$(document).on('click', '.btn-toggle', function () {
  const id = $(this).data('id');

  Swal.fire({
    title: 'Ubah status produk?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya',
    cancelButtonText: 'Batal'
  }).then((r) => {
    if (!r.isConfirmed) return;

    $.ajax({
      url: `/master_produk/${id}/toggle`,
      method: 'PUT',
      data: { _token: '{{ csrf_token() }}' },
      success: function(res){
        Swal.fire('Berhasil', res.message, 'success').then(()=>location.reload());
      },
      error: function(xhr){
        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
      }
    });
  });
});

$(document).on('click', '.btn-delete', function () {
  const id = $(this).data('id');

  Swal.fire({
    title: 'Hapus produk permanen?',
    text: 'Hanya bisa jika produk belum pernah dipakai transaksi.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((r) => {
    if (!r.isConfirmed) return;

    $.ajax({
      url: `/master_produk/${id}`,
      method: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: function(res){
        Swal.fire('Berhasil', res.message, 'success').then(()=>location.reload());
      },
      error: function(xhr){
        Swal.fire('Tidak bisa', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
      }
    });
  });
});
</script>

@endsection
