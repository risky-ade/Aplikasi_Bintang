@extends('layouts.main')
@section('content')
@php
    $isEdit = false;
    $isLocked = $isLocked ?? false;
@endphp
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Item Master Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Tambah Item</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
   
        <section class="content">
        <div class="container">
            <form action="{{ url('/master_produk') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('master_produk.form', ['master_produk' => null])
                <div class="card-footer text-right">
                    <button type="submit"  id="btnSubmit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('/master_produk') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
        </section>
      
    </div>
   
@endsection
