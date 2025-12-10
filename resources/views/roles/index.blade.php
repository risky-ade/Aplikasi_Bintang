@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Manajemen Role</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

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

    </div>
  </section>
</div>
@endsection