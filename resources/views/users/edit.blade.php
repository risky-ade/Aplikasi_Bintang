@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Edit User</h3>
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

      <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
          <div class="card-body">

            <div class="form-group">
              <label>Nama</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" class="form-control"
                     value="{{ old('username', $user->username) }}" required>
              <small class="text-muted">Huruf/angka/dash/underscore</small>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
              <label>Foto</label><br>
              @if($user->photo)
                <img src="{{ asset('storage/'.$user->photo) }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
              @else
                <small class="text-muted">Belum ada foto</small>
              @endif
              <input type="file" name="photo" class="form-control mt-2" accept="image/*">
              <small class="text-muted">jpg/png/webp max 2MB</small>
            </div>

            @php
              // proteksi superadmin default (id=1) tidak bisa ganti role
              $isDefaultSuperadmin = ((int)$user->id === 1);
            @endphp

            @if(!$isDefaultSuperadmin)
              <div class="form-group">
                <label>Role</label>
                <select name="role_id" class="form-control" required>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                      {{ $role->label ?? $role->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            @else
              <div class="alert alert-info">
                Akun superadmin default tidak dapat diubah rolenya.
              </div>
            @endif

            <hr>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Password Baru (opsional)</label>
                  <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Konfirmasi Password Baru</label>
                  <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi">
                </div>
              </div>
            </div>

          </div>

          <div class="card-footer d-flex justify-content-end">
            <div>
              <button type="submit" class="btn btn-primary">Simpan</button>
              <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
          </div>
        </div>
      </form>

    </div>
  </section>
</div>
@endsection