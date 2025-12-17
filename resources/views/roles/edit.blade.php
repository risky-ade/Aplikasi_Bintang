@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Atur Hak Akses: {{ $role->label ?? $role->name }}</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="card">
          <div class="card-body">
            <div class="row">
              @foreach($permissions as $perm)
                <div class="col-md-4 mb-2">
                  <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="permissions[]"
                           value="{{ $perm->id }}"
                           id="perm_{{ $perm->id }}"
                           {{ in_array($perm->id, $rolePermIds) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $perm->id }}">
                      <strong>{{ $perm->label ?? $perm->name }}</strong><br>
                      <small class="text-muted">{{ $perm->name }}</small>
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection