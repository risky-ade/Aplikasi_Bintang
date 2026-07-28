@extends('layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0">Stok Opname</h1> --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Produk</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mt-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <div class="card">
                <div class="card-header">
                    <h3>Detail Produk / {{ $masterProduk->nama_produk }}</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                    <div class="col">
                        @if($masterProduk->gambar)
                        <img src="{{ asset('storage/'.$masterProduk->gambar) }}" width="300" alt="gambar">
                        @else
                        -
                        @endif
                    </div>
                    <div class="col">
                    <p><b>Nama Produk:</b> {{ $masterProduk->nama_produk }}</p>
                    <p><b>Kategori:</b> {{ $masterProduk->kategori->nama_kategori }}</p>
                    <p><b>Satuan:</b> {{ $masterProduk->satuan->jenis_satuan }}</p>
                    <p><b>Harga dasar:</b> {{ rupiah($masterProduk->harga_dasar, 0, ',', '.') }}</p>
                    <p><b>Harga jual:</b> {{ rupiah($masterProduk->harga_jual, 0, ',', '.') }}</p>
                    <p><b>Stok:</b> {{ $masterProduk->stok }}</p>
                    <p><b>Stok Minimal:</b> {{ $masterProduk->stok_minimal }}</p>
                    <p><b>Deskripsi:</b> {{ $masterProduk->deskripsi ?? '-' }}</p>
                    </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('master_produk.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
    </div>
</section>
</div>
@endsection