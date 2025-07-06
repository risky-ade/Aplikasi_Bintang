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
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                                        <i class="fas fa-plus"></i> Tambah Satuan
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Satuan</th>
                                            <th>Keterangan Satuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($satuans as $index => $satuan)
                                        <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $satuan->jenis_satuan }}</td>
                                        <td>{{ $satuan->keterangan_satuan }}</td>
                                        <td>
                                            <!-- Edit -->
                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalEdit{{ $satuan->id }}">
                                            <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Hapus -->
                                            <button class="btn btn-danger btn-sm" type="button" onclick="konfirmasiHapus({{ $satuan->id }}, '{{ $satuan->jenis_satuan }}')"><i class="fa fa-trash"></i> </button>
                                        </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modalEdit{{ $satuan->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('units.update', $satuan->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title">Edit Satuan</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama Satuan</label>
                                                    <input type="text" name="jenis_satuan" class="form-control" value="{{ $satuan->jenis_satuan }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Keterangan</label>
                                                    <input type="text" name="keterangan_satuan" class="form-control" value="{{ $satuan->keterangan_satuan }}">
                                                </div>
                                                </div>
                                                <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                        </div>
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
    <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Tambah Satuan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis Satuan</label>
                        <input type="text" name="jenis_satuan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan_satuan" class="form-control">
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        // SweetAlert Sukses Tambah/Edit
        @if(session('success_add'))
            Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success_add') }}',
            showConfirmButton: false,
            timer: 2000
            });
        @endif

        @if(session('success_update'))
            Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success_update') }}',
            showConfirmButton: false,
            timer: 2000
            });
        @endif
    </script>

@endsection
