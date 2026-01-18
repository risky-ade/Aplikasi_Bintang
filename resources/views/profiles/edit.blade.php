@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Profil Perusahaan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profil</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <form method="POST" action="{{ route('profil.update') }}">
      @csrf

      <div class="card">
        <div class="card-body">

          <div class="form-group">
            <label>Nama Perusahaan</label>
            <input type="text" name="nama_perusahaan" class="form-control"
                  value="{{ old('nama_perusahaan',$profil->nama_perusahaan) }}" required>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label>Email</label>
              <input type="email" name="email" class="form-control"
                    value="{{ old('email',$profil->email) }}">
            </div>
            <div class="col-md-6">
              <label>Telepon</label>
              <input type="text" name="telepon" class="form-control"
                    value="{{ old('telepon',$profil->telepon) }}">
            </div>
          </div>

          <div class="form-group mt-2">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control">{{ old('alamat',$profil->alamat) }}</textarea>
          </div>

          <div class="row mt-2">
            <div class="col-md-6">
              <label>Nama Bank</label>
              <input type="text" name="nama_bank" class="form-control"
                    value="{{ old('nama_bank',$profil->nama_bank) }}">
            </div>
            <div class="col-md-6">
              <label>No Rekening</label>
              <input type="text" name="no_rekening" class="form-control"
                    value="{{ old('no_rekening',$profil->no_rekening) }}">
            </div>
          </div>

        </div>

        <div class="card-footer text-right">
          <button class="btn btn-primary">Simpan</button>
        </div>
      </div>
      </form>
    </section>
  </div>
</div>
@endsection