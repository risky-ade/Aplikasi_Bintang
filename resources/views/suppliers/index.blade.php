@extends('layouts.main')
@section('content')
<style>
.nowrap th, .nowrap td { white-space: nowrap; }

.alamat-col {
    max-width: 250px;
    -webkit-line-clamp: 1; 
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pemasok</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pemasok</li>
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
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-2">Tambah Pemasok</a>
                  </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover w-100 nowrap" id="DataTable">
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
                              <td class="alamat-col" data-bs-toggle="tooltip" title="{{ $p->alamat }}">{{ $p->alamat }}</td>
                              {{-- <td data-bs-toggle="tooltip" title="{{ $p->alamat }}">{{\Illuminate\Support\Str::limit( $p->alamat,16, '...')  }}</td> --}}
                              <td>{{ $p->kota }}</td>
                              <td>{{ $p->provinsi }}</td>
                              <td>
                                  <a href="{{ route('suppliers.edit', $p->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $p->id }}" data-nama="{{ $p->nama }}"><i class="fas fa-trash"></i></button>
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
    $('#DataTable').DataTable({
      autoWidth: false,    
      responsive: false,    
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,1,2,3,4,6,7,8], className: 'text-nowrap' },
        { targets: [5], width: '200px' }
      ],

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

  $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
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