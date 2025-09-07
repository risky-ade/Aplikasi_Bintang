@extends('layouts.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Pelanggan</h1>
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
       <!-- Main content -->
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

                <form action="{{ route('customers.store') }}" method="POST">
                    <div class="card">
                        <div class="card-body">
                            @include('customers.form', ['submitText' => 'Simpan'])
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection