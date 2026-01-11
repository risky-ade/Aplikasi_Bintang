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
          {{-- <button type="button" class="btn btn-danger mb-3" id="btn-clear-processed">
            <i class="fas fa-broom"></i> Hapus Request Selesai
          </button> --}}
          <table class="table table-bordered" id="DataTable">
            <thead class="bg-secondary text-white">
              <tr>
                <th>No</th>
                <th>Login</th>
                <th>User ID</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($requests as $r)
                <tr>
                  <td>{{ $loop->iteration }}</td>
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
                    <td>
                      <button type="button" class="btn btn-sm btn-danger btn-del-request" data-id="{{ $r->id }}">
                        <i class="fas fa-trash"></i>
                      </button>
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
<script>
$(document).on('click', '.btn-del-request', function(){
  const id = $(this).data('id');

  Swal.fire({
    title: 'Hapus request ini?',
    text: 'Data request akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((r) => {
    if (!r.isConfirmed) return;

    $.ajax({
      url: `{{ url('/password_reset_requests') }}/${id}`,
      method: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: function(res){
        Swal.fire('Berhasil', res.message, 'success')
          .then(()=>location.reload());
      },
      error: function(xhr){
        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
      }
    });
  });
});

$('#btn-clear-processed').on('click', function(){
  Swal.fire({
    title: 'Hapus semua request yang sudah diproses?',
    text: 'Request selesai akan dibersihkan agar tidak menumpuk.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((r) => {
    if (!r.isConfirmed) return;

    $.ajax({
      url: `{{ route('password_reset_requests.destroyProcessed') }}`,
      method: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: function(res){
        Swal.fire('Berhasil', res.message, 'success')
          .then(()=>location.reload());
      },
      error: function(xhr){
        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
      }
    });
  });
});
</script>
@endsection