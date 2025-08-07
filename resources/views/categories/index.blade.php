@extends('layouts.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Kategori Produk</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Kategori</li>
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
                            <button class="btn btn-primary me-md-2" onclick="showCreateModal()">
                                <i class="fas fa-solid fa-plus"></i>Tambah Kategori</button>
                        </div>
                    </div>
                    <div class="card-body">
                    <table class="table table-bordered" id="kategoriTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $item)
                                <tr>
                                    <td scope="item">{{ $loop->iteration }}</td>
                                    <td>{{ $item->kode_kategori }}</td>
                                    <td>{{ $item->nama_kategori }}</td>
                                    <td>
                                        {{-- <button class="btn btn-sm btn-info" onclick="editKategori({{ $item }})">Edit</button> --}}
                                        <button class="btn btn-danger btn-sm" type="button" onclick="konfirmasiHapus({{ $item->id }}, '{{ $item->nama_kategori }}')"><i class="fa fa-trash"></i> </button>
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

    <!-- Modal Form -->
    <div class="modal fade" id="kategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="kategoriForm">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Form Kategori</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" id="kategori_id">
                <div class="mb-3">
                    <label>Kode Kategori</label>
                    <input type="text" class="form-control" id="kode_kategori" name="kode_kategori" readonly>
                </div>
                <div class="mb-3">
                    <label>Nama Kategori</label>
                    <input type="text" class="form-control" name="nama_kategori" id="nama_kategori" required>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </div>
        </form>
    </div>
    </div>

    <!-- Modal konfirmasi hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus <strong id="namaKategori"></strong>?</p>
            <input type="hidden" id="idKategori">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-danger" onclick="deleteKategori()">Hapus</button>
        </div>
        </div>
    </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
</div>
<!-- ./wrapper -->


<script>
function showCreateModal() {
    $('#kategoriForm')[0].reset();
    $('#kategori_id').val('');
    $('#kode_kategori').val('Auto'); // tampilkan "Auto" sebagai placeholder
    $('#kategoriModal').modal('show');
}

function editKategori(data) {
    $('#kategori_id').val(data.id);
    $('#kode_kategori').val(data.kode_kategori);
    $('#nama_kategori').val(data.nama_kategori);
    $('#kategoriModal').modal('show');
}

$('#kategoriForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#kategori_id').val();
    let url = id ? `/categories/${id}` : '/categories';
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: $('#kategoriForm').serialize(),
        success: function(response) {
            Swal.fire('Sukses!', response.message, 'success').then(() => location.reload());
        },
       error: function(xhr) {
        let msg = 'Terjadi kesalahan';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            msg = Object.values(xhr.responseJSON.errors).join('<br>');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: msg,
        });
    }
    });
});
</script>

{{-- script delete --}}
    <script>
        function konfirmasiHapus(id, nama_kategori) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Kategori "${nama_kategori}" akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusKategori(id);
                }
            });
        }

        function hapusKategori(id) {
            fetch(`/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: 'Tidak dapat menghapus data.',
                    icon: 'error'
                });
            });
        }
    </script>
@endsection