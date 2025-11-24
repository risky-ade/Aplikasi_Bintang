@extends('layouts.main')
@section('content')
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Retur Pembelian</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active">Retur Pembelian</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

      <div class="card">
        <div class="card-header d-flex justify-content-end">
          <a href="{{ route('retur-pembelian.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Retur
          </a>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead class="bg-dark text-white">
              <tr>
                <th>No</th>
                <th>No Retur</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Pemasok</th>
                <th>Total Retur</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($returns as $index => $retur)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $retur->no_retur }}</td>
                  <td>{{ $retur->pembelian->no_faktur ?? '-' }}</td>
                  <td>{{ $retur->tanggal_retur->format('d-m-Y') }}</td>
                  <td>{{ $retur->pembelian->pemasok->nama ?? '-' }}</td>
                  <td>Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('retur-pembelian.show', $retur->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    
                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $retur->id }}" data-no_retur="{{ $retur->no_retur }}">
                        <i class="fas fa-trash"></i>
                    </button>                                        
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Belum ada data retur penjualan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </section>
  </div>
</div>
{{-- script delete --}}
<script>
  $(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let no_retur = $(this).data('no_retur');

    Swal.fire({
      title: 'Yakin ingin hapus?',
      text: `Retur "${no_retur}" akan dihapus.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/purchases/purchases_retur/${id}`,
          type: 'POST',
          data: {
             _method: 'DELETE',
            _token: '{{ csrf_token() }}'
          },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
          },
          error: function (xhr) {
            let res = xhr.responseJSON;
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: res.message || 'Terjadi kesalahan.',
            });
          }
        });
      }
    });
  });
</script>
@endsection