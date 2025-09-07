@extends('layouts.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pemasok</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pemasok</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
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
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-2">Tambah Pemasok</a>
                  </div>
                </div>
                <div class="card-body">
                <table class="table table-bordered table-striped" id="DataTable">
                  <thead class="bg-secondary text-white">
                      <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Email</th>
                          <th>NPWP</th>
                          <th>No HP</th>
                          <th>Alamat</th>
                          <th>Kota</th>
                          <th>Provinsi</th>
                          <th>Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($pemasoks as $p)
                          <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $p->nama }}</td>
                              <td>{{ $p->email }}</td>
                              <td>{{ $p->npwp }}</td>
                              <td>{{ $p->no_hp }}</td>
                              <td>{{ $p->alamat }}</td>
                              <td>{{ $p->kota }}</td>
                              <td>{{ $p->provinsi }}</td>
                              <td>
                                  <a href="{{ route('suppliers.edit', $p->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                  {{-- <form action="{{ route('customers.destroy', $p->id) }}" method="POST" style="display:inline;">
                                      @csrf @method('DELETE')
                                      <button onclick="return confirm('Hapus pelanggan ini?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form> --}}
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $p->id }}" data-nama="{{ $p->nama }}"><i class="fas fa-trash"></i></button>
                              </td>
                          </tr>
                      @endforeach
                  </tbody>
              </table>
              </div>
              {{ $pemasoks->links() }}
              </div>
            </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</div>
<script>
  $(document).ready(function() {
    $('#DataTable').DataTable({
      "pageLength": 10,
      "lengthMenu": [10, 15, 25, 50, 100],
      "language": {
        "search": "Cari:",
        "lengthMenu": "Tampilkan _MENU_ baris per halaman",
        "zeroRecords": "Data tidak ditemukan",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Tidak ada data",
        "infoFiltered": "(disaring dari total _MAX_ data)",
        "paginate": {
          "next": "Berikutnya",
          "previous": "Sebelumnya"
        }
      },
    });
  });
</script>
{{-- script delete --}}
<script>
  $(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let nama = $(this).data('nama');

    Swal.fire({
      title: 'Yakin ingin hapus?',
      text: `Pemasok "${nama}" akan dihapus.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/suppliers/${id}`,
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