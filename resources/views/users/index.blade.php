@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manajemen Pengguna</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pengguna</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="card">
        <div class="card-header">
            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
              <i class="fas fa-plus"></i> Tambah User
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-danger mb-3">
              Role User
            </a>
            <a href="{{ route('password_reset_requests.index') }}" class="btn btn-warning mb-3">
              Password Reset Request
            </a>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped" id="DataTable">
            <thead class="bg-secondary text-white">
              <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->username }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->role->label ?? $user->role->name ?? '-' }}</td>
                  <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
               
                    @if($user->id !== 1)
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;"
                          onsubmit="return confirm('Yakin hapus user ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
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
</div>

<script>
  $(document).ready(function() {
    $('#DataTable').DataTable({
      "pageLength": 10,
      "lengthMenu": [10, 15, 25, 50, 100],
      "language": {
        "search": "Cari:",
        "lengthMenu": "Tampilkan _MENU_ baris per halaman",
        "zeroRecords": "Data tidak ditemukan",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Tidak ada data",
        "infoFiltered": "(disaring dari total _MAX_ data)",
        "paginate": {
          "next": "Berikutnya",
          "previous": "Sebelumnya"
        }
      },
    });
  });
</script>
@endsection