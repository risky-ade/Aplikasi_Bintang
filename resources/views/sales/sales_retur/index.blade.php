@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Retur Penjualan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active">Retur Penjualan</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <form method="GET" action="{{ route('retur-penjualan.index') }}" class="mb-3">
              <div class="row">
                <div class="col-md-2 mb-2">
                  <label>Tanggal Awal</label>
                  <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-2 mb-2">
                  <label>Tanggal Akhir</label>
                  <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3 mb-2">
                  <label>No Faktur</label>
                  <input type="text" name="no_faktur" class="form-control" placeholder="Cari no faktur" value="{{ request('no_faktur') }}">
                </div>
                <div class="col-md-3 mb-2">
                  <label>Nama Pelanggan</label>
                  <input type="text" name="pelanggan" class="form-control" placeholder="Cari pelanggan" value="{{ request('pelanggan') }}">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="{{ route('retur-penjualan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync"></i>
                  </a>
                </div>
              </div>
            </form>
            <div class="d-flex justify-content-end">
              <a href="{{ route('retur-penjualan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Retur
              </a>
            </div>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-striped" id="DataTable">
              <thead class="bg-dark text-white">
                <tr>
                  <th>No</th>
                  <th>No Retur</th>
                  <th>No Faktur</th>
                  <th>Tanggal</th>
                  <th>Pelanggan</th>
                  <th>Total Retur</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($returs as $index => $retur)
                  @php
                    $locked = $retur->penjualan && ($retur->penjualan->approved_at || $retur->penjualan->status_pembayaran === 'Lunas');
                  @endphp
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $retur->no_retur }}</td>
                    <td>{{ $retur->penjualan->no_faktur ?? '-' }}</td>
                    <td>{{ $retur->tanggal_retur->format('d-m-Y') }}</td>
                    <td>{{ $retur->penjualan->pelanggan->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                    <td class="text-nowrap">
                      <a href="{{ route('retur-penjualan.show', $retur->id) }}" class="btn btn-sm btn-info" title="Lihat">
                        <i class="fas fa-eye"></i>
                      </a>

                      @if ($locked)
                        <button class="btn btn-sm btn-warning" disabled title="Faktur sudah lunas/approve">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" disabled title="Faktur sudah lunas/approve">
                          <i class="fas fa-trash"></i>
                        </button>
                      @else
                        <a href="{{ route('retur-penjualan.edit', $retur->id) }}" class="btn btn-sm btn-warning" title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $retur->id }}" data-no_retur="{{ $retur->no_retur }}" title="Hapus">
                          <i class="fas fa-trash"></i>
                        </button>
                      @endif
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
          url: `/sales/sales_retur/${id}`,
          type: 'POST',
          data: {
            _method: 'DELETE',
            _token: '{{ csrf_token() }}'
          },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: res.message || 'Retur berhasil dihapus.',
              timer: 1500,
              showConfirmButton: false
            }).then(() => location.reload());
          },
          error: function (xhr) {
            let res = xhr.responseJSON || {};
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
<script>
  $(document).ready(function() {
    $('#DataTable').DataTable({
      autoWidth: false,
      responsive: false,
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,1,2,4,5,6], className: 'text-nowrap' },
        { targets: [3], width: '220px' }
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ baris per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari total _MAX_ data)",
        paginate: { next: "Berikutnya", previous: "Sebelumnya" }
      },
    });
  });
</script>
@endsection