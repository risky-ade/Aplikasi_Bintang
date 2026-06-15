@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Manajemen Role</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Role</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card">
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Nama Role</th>
                <th>Label</th>
                <th>Jumlah Permission</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td>{{ $role->name }}</td>
                  <td>{{ $role->label }}</td>
                  <td>{{ $role->permissions->count() }}</td>
                  <td>
                    @if($role->name !== 'superadmin')
                      <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary">
                        Atur Hak Akses
                      </a>
                    @else
                      <span class="badge badge-success">Full Access</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-right">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </section>
</div>
@endsection