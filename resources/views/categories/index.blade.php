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
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create"><i class="fas fa-solid fa-plus"></i>
                                  Tambah Data
                              </button>
                          </div>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body">
                          <table id="example2" class="table table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>Kategori Id</th>
                                      <th>Kode Kategori</th>
                                      <th>Nama Kategori</th>
                                      <th>Aksi</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($category as $row)
                                      <tr>
                                          <th scope="row">{{ $loop->iteration }}</th>
                                          <td>{{ $row->kode_kategori }}</td>
                                          <td>{{ $row->nama_kategori }}</td>
                                          <td class="text-center">
                                              <a href="javascript:;" data-id="<?= $row->id ?>" class="btn btn-info btn-edit " data-toggle="modal" id="edit" type="button"><i class="fa fa-edit"></i> </a>

                                             <button class="btn btn-danger btn-sm" type="button" onclick="konfirmasiHapus({{ $row->id }}, '{{ $row->nama_kategori }}')"><i class="fa fa-trash"></i> </button>

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
          <!-- Modal tambah -->
    <div class="modal fade" id="modal-create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Tambah Kategori</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="/categories" enctype="multipart/form-data">
                    @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Kode Kategori</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan Kode Kategori" name="kode_kategori"> 
                  </div>
                  <div class="form-group">
                    <label for="#">Nama Kategori</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan Nama Kategori" name="nama_kategori">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary toastrDefaultSuccessadd">Simpan</button>
                </div>
              </form>
            </div>
        </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    {{-- modal edit --}}
    <div class="modal fade" id="modal-update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Edit Kategori</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url('/categories/update') }}" enctype="multipart/form-data">
                    @csrf
                <input type="text" id="idKategori" name="idKategori" hidden>
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Kode Kategori</label>
                    <input type="text" class="form-control" id="kode_kategori_up" name="kode_kategori_up" placeholder="Masukkan kode kategori">
                  </div>
                  <div class="form-group">
                    <label for="#">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori_up" name="nama_kategori_up" placeholder="Masukkan nama kategori">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary toastrDefaultSuccessedit">Simpan</button>
                </div>
              </form>
            </div>
        </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2024-2025 <a href="#">CV.Bintang Empat</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

    <script>
        $(function() {
            var Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 6000
            });

            $('.toastrDefaultSuccessadd').click(function() {
            toastr.success('Sukses, Kategori Berhasil Ditambahkan.')
            });
            $('.toastrDefaultSuccessedit').click(function() {
            toastr.success('Sukses, Kategori Berhasil Diupdate.')
            });
            $('.toastrDefaultInfo').click(function() {
            toastr.info('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
            });
            $('.toastrDefaultError').click(function() {
            toastr.error('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
            });
            $('.toastrDefaultWarning').click(function() {
            toastr.warning('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
            });
        });
    </script>
     <script>
        // Edit Data
        $(document).on('click', '.btn-edit', function() { //edit ada di class
            // alert('test');
            var id = $(this).data(
                'id'
            ); //data dan id diperoleh dari button "data-id" baris 38. serta di controller $response['data'] = $kur;
            $.ajax({
                // console.log(id);
                url: "{{ url('/categories/edit') }}" + '/' + id,
                type: 'get',
                dataType: 'json',
                data: {},
                beforeSend: function() {},
                success: function(data) {
                    // console.log(data.data)
                    $('#modal-update').modal('show'); //menampilkan modal
                    $('#kode_kategori_up').val(data.data.kode_kategori);
                    $('#nama_kategori_up').val(data.data.nama_kategori);
                    $('#idKategori').val(data.data.id);
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