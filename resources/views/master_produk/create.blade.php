@extends('layouts.main')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Item Master Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Tambah Item</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
        <div class="container">
            <form action="{{ url('/master_produk') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('master_produk.form', ['master_produk' => null])
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ url('/master_produk') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
        </section>
        {{-- /Main content --}}
    </div>
@endsection
