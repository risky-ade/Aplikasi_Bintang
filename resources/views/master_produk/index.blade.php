@extends('layouts.main')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
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
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/master_produk/create">
                                        <button class="btn btn-primary me-md-2" type="button"><i
                                                class="fas fa-solid fa-plus"></i>Tambah Data</button>
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Gambar</th>
                                            <th>Nama Produk</th>
                                            <th>Deskripsi</th>
                                            <th>Kategori</th>
                                            <th>Satuan</th>
                                            <th>Harga Dasar</th>
                                            <th>Stok</th>
                                            <th>Status Stok</th>
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
                                                <td>{{ $row->deskripsi }}</td>
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
                                                    {{-- <a href="{{ url('/master_produk/'.$row->id.'/edit') }}" class="btn btn-sm btn-info"
                                                        type="button"><i class="fa fa-edit"></i> </a> --}}
                                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $row->id }}" data-nama="{{ $row->nama_produk }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>                                        
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>

        </section>
        <!-- /.content -->
    </div>

    </div>
{{-- script delete --}}
<script>
  $(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let nama = $(this).data('nama');

    Swal.fire({
      title: 'Yakin ingin hapus?',
      text: `Produk "${nama}" akan dihapus.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/master_produk/${id}`,
          type: 'DELETE',
          data: {
            _token: '{{ csrf_token() }}'
          },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
          },
          error: function (xhr) {
            let res = xhr.responseJSON;
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: res.message || 'Terjadi kesalahan.',
            });
          }
        });
      }
    });
  });
</script>
@endsection
