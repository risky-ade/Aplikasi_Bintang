@extends('layouts.main')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Satuan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Satuan</li>
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
                                            <th>Satuan Id</th>
                                            <th>Jenis Satuan</th>
                                            <th>Keterangan Satuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unit as $row)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $row->jenis_satuan }}</td>
                                                <td>{{ $row->keterangan_satuan }}</td>
                                                <td class="text-center">
                                                    {{-- <a href="{{ url('/edit-produk') }}" class="btn btn-info" type="button" data-toggle="modal" data-target="#modal-update"><i class="fa fa-edit"></i> </a> --}}
                                                      <a href="javascript:;" data-id="<?= $row->id ?>" class="btn btn-info btn-edit " data-toggle="modal" id="edit" type="button"><i class="fa fa-edit"></i> </a> 

                                                    {{-- <a href="{{ url('/delete-produk') }}" class="btn btn-danger "
                                                        type="button"><i class="fa fa-trash"></i> </a> --}}
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="konfirmasiHapus({{ $row->id }}, '{{ $row->jenis_satuan }}')"><i class="fa fa-trash"></i> </button>
                                                        
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
    <!-- Modal konfirmasi hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus <strong id="jenisSatuan"></strong>?</p>
            <input type="hidden" id="idSatuan">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-danger" onclick="deleteSatuan()">Hapus</button>
        </div>
        </div>
    </div>
    </div>
    <!-- Modal tambah -->
    <div class="modal fade" id="modal-create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Tambah Satuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="/units" enctype="multipart/form-data">
            
                    @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Jenis Satuan</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan jenis satuan" name="jenis_satuan"> 
                  </div>
                  <div class="form-group">
                    <label for="#">Keterangan Satuan</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan keterangan satuan" name="keterangan_satuan">
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
    <div class="modal fade" id="modal-update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Edit Satuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/units/update') }}" enctype="multipart/form-data">
                    @csrf
                <input type="hidden" id="idSatuan" name="idSatuan">
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Jenis Satuan</label>
                    <input type="text" class="form-control" id="jenis_satuan_up" name="jenis_satuan_up" placeholder="Masukkan jenis satuan">
                  </div>
                  <div class="form-group">
                    <label for="#">Keterangan Satuan</label>
                    <input type="text" class="form-control" id="keterangan_satuan_up" name="keterangan_satuan_up" placeholder="Masukkan keterangan satuan">
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
    <!-- /.modal -->
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
        // Edit Data
        $(document).on('click', '.btn-edit', function() { //edit ada di class
            // alert('test');
            var id = $(this).data(
                'id'
            ); //data dan id diperoleh dari button "data-id" ;
            $.ajax({
                // console.log(id);
                url: "{{ url('/units/edit') }}" + '/' + id,
                type: 'get',
                dataType: 'json',
                data: {},
                beforeSend: function() {},
                success: function(data) {
                    // console.log(data.data)
                    $('#modal-update').modal('show'); //menampilkan modal
                    $('#jenis_satuan_up').val(data.data.jenis_satuan);
                    $('#keterangan_satuan_up').val(data.data.keterangan_satuan);
                    $('#idSatuan').val(data.data.id);
                    console.log('ID Satuan:',data.data.id);
                }
            });
        });
    </script>
    {{-- script delete --}}
    <script>
        function konfirmasiHapus(id, jenis_satuan) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Satuan "${jenis_satuan}" akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusSatuan(id);
                }
            });
        }

        function hapusSatuan(id) {
            fetch(`/units/${id}`, {
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

    <script>
        $(function() {
            var Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 6000
            });

            $('.swalDefaultSuccessadd').click(function() {
            Toast.fire({
                icon: 'success',
                title: 'Satuan Berhasil Ditambahkan.'
            })
            });
            $('.swalDefaultSuccessedit').click(function() {
            Toast.fire({
                icon: 'success',
                title: 'Satuan Berhasil Diupdate.'
            })
            });
            $('.swalDefaultInfo').click(function() {
            Toast.fire({
                icon: 'info',
                title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            })
            });
            $('.swalDefaultError').click(function() {
            Toast.fire({
                icon: 'error',
                title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            })
            });
            $('.swalDefaultWarning').click(function() {
            Toast.fire({
                icon: 'warning',
                title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            })
            });
            $('.swalDefaultQuestion').click(function() {
            Toast.fire({
                icon: 'question',
                title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            })
            });

            $('.toastrDefaultSuccessadd').click(function() {
            toastr.success('Berhasil, Satuan Ditambahkan.')
            });
            $('.toastrDefaultSuccessedit').click(function() {
            toastr.success('Berhasil, Satuan Diupdate.')
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

    
@endsection
