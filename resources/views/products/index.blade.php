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
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Daftar Produk</li>
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
                                    <a href="/products/create">
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
                                            <th>Item Id</th>
                                            <th>Nama Produk</th>
                                            <th>Harga Satuan (Rp)</th>
                                            <th>Kategori</th>
                                            <th>Satuan</th>
                                            <th>Stok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produk as $row)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $row->masterProduk->nama_produk ??'Null' }}</td>
                                                <td>{{ $row->masterProduk->harga ??'Null' }}</td>
                                                <td>{{ $row->masterKategori->nama_kategori ??'Null' }}</td>
                                                <td>{{ $row->masterSatuan->jenis_satuan ??'Null' }}</td>
                                                <td>{{ $row->stok }}</td>
                                                <td class="text-center">
                                                    <a href="{{ url('/edit-produk') }}" class="btn btn-info"
                                                        type="button"><i class="fa fa-edit"></i> </a>
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
