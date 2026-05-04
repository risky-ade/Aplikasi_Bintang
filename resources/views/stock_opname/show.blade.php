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
                        {{-- <h1 class="m-0">Stok Opname</h1> --}}
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
            <div class="card">
                <div class="card-header">
                    <h3>Detail Stock Opname</h3>
                </div>

                <div class="card-body">

                    <p><b>No Opname:</b> {{ $opname->no_opname }}</p>
                    <p><b>Tanggal:</b> {{ $opname->tanggal }}</p>
                    <p><b>Status:</b> {{ $opname->status }}</p>
                    

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Stok Sistem</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opname->details as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->produk->nama_produk }}</td>
                                <td>{{ $d->stok_sistem }}</td>
                                <td>{{ $d->stok_fisik }}</td>
                                <td>
                                    <span class="badge badge-{{ $d->selisih < 0 ? 'danger' : 'success' }}">
                                        {{ $d->selisih }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p><b>Catatan:</b> {{ $opname->catatan }}</p>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('stock_opname.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
    </div>
</section>
</div>
@endsection