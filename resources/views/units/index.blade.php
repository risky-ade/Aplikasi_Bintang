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

            {{-- @if (session()->has('success'))
    <div class="alert alert-success col-lg-8" role="alert">
      {{ session('success') }}
    </div>
  @endif --}}
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    {{-- <a href="/tambah-produk">
                                        <button class="btn btn-primary me-md-2" type="button"><i
                                                class="fas fa-solid fa-plus"></i>Tambah Data</button>
                                    </a> --}}
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
                                                    <a href="{{ url('/edit-produk') }}" class="btn btn-info" type="button" data-toggle="modal" data-target="#modal-update"><i class="fa fa-edit"></i> </a>
                                                    <a href="{{ url('/delete-produk') }}" class="btn btn-danger "
                                                        type="button"><i class="fa fa-trash"></i> </a>
                                                    {{-- <a href="javascript:;" data-id="<?= $row->id ?>" class="btn btn-warning " id="editKelas"
                                        type="button"> <i class="icon-copy fa fa-edit" aria-hidden="true"></i> </a>
                                    <a href="javascript:;" data-id="<?= $row->id ?>" id="btn-hapus"
                                        class="btn btn-danger"><i class="fa fa-trash"></i>
                                        </a> --}}
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
                <form>
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Jenis Satuan</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan jenis satuan">
                  </div>
                  <div class="form-group">
                    <label for="#">Keterangan Satuan</label>
                    <input type="text" class="form-control" id="#" placeholder="Masukkan keterangan satuan">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
                <form method="post" action="" enctype="multipart/form-data">
                    {{ method_field('put') }}
                    @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="#">Jenis Satuan</label>
                    <input type="text" class="form-control" id="jenis_satuan" name="jenis_satuan" placeholder="Masukkan jenis satuan">
                  </div>
                  <div class="form-group">
                    <label for="#">Keterangan Satuan</label>
                    <input type="text" class="form-control" id="keterangan_satuan" name="keterangan_satuan" placeholder="Masukkan keterangan satuan">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
@endsection
