@extends('layouts.main')

@section('content')
<style>
    thead tr {
        background-color: #001f3f;
        color: white;
    }

    .form-control {
        height: 36px;
        padding: 0.25rem 0.5rem;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .produk-column {
        min-width: 240px;
    }

    .number-input {
        text-align: right;
    }
</style>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Stok Opname</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Stok Opname</li>
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
            <form action="{{ route('stock_opname.update',$opname->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <h3>Edit Stock Opname</h3>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $opname->tanggal }}" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label>Catatan</label>
                            <input type="text" name="catatan" value="{{ $opname->catatan }}" class="form-control">
                        </div>
                    </div>

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok Sistem</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opname->details as $d)
                            <tr>
                                <td>{{ $d->produk->nama_produk }}</td>
                                <td>{{ $d->stok_sistem }}</td>
                                <td>
                                    <input type="number" name="stok_fisik[]" 
                                    value="{{ $d->stok_fisik }}" class="form-control">
                                </td>
                                <td>{{ $d->selisih }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('stock_opname.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    <button class="btn btn-primary btn-sm">Update</button>
                </div>

            </div>
            </form>

        </div>
</section>
</div>
@endsection
