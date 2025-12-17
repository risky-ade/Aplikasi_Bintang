@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3>Password Reset Requests</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card">
        <div class="card-body">
          <table class="table table-bordered" id="DataTable">
            <thead class="bg-secondary text-white">
              <tr>
                <th>Login</th>
                <th>User ID</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($requests as $r)
                <tr>
                  <td>{{ $r->login }}</td>
                  <td>{{ $r->user_id ?? '-' }}</td>
                  <td>{{ $r->note ?? '-' }}</td>
                  <td>
                    <span class="badge {{ $r->status==='pending'?'badge-warning':'badge-success' }}">
                      {{ $r->status }}
                    </span>
                  </td>
                  <td>
                    @if($r->status==='pending')
                      <form method="POST" action="{{ route('password_reset_requests.reset', $r->id) }}">
                        @csrf
                        <div class="row">
                          <div class="col-md-4">
                            <input type="password" name="new_password" class="form-control" placeholder="Password baru" required>
                          </div>
                          <div class="col-md-4">
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Konfirmasi" required>
                          </div>
                          <div class="col-md-4">
                            <button class="btn btn-primary btn-sm">Reset</button>
                          </div>
                        </div>
                      </form>
                    @else
                      <small class="text-muted">Handled at: {{ $r->handled_at }}</small>
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