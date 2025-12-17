@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Tambah User</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="card">
          <div class="card-body">
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control"
                        value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control"
                        value="{{ old('username') }}" required>
                <small class="text-muted">Contoh: staf_gudang, admin1</small>
                </div>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control"
                     value="{{ old('email') }}" required>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
              <label>Role</label>
              <select name="role_id" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->label ?? $role->name }}
                  </option>
                @endforeach
              </select>
            </div>
            </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Confirm Password</label>
                  <input type="password" name="password_confirmation" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Foto (opsional)</label>
              <input type="file" name="photo" class="form-control" accept="image/*">
              <small class="text-muted">jpg/png/webp, max 2MB</small>
            </div>
          </div>

          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
          </div>
        </div>
      </form>

    </div>
  </section>
</div>
@endsection