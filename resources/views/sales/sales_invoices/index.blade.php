@extends('layouts.main')
@section('content')
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
<style>
  .dropdown-menu {
    max-width: 220px;
    word-wrap: break-word;
  }
</style>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Faktur Penjualan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Faktur Penjualan</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/sales/sales_invoices/create">
                  <button class="btn btn-primary me-md-2" type="button"><i
                    class="fas fa-solid fa-plus"></i>Tambah Data</button>
                </a>
              </div>
            </div>
            <div class="card-body">
              <form method="GET" action="{{ route('penjualan.index') }}" class="mb-3">
              <div class="row">
                <div class="col-md-2 mb-3">
                  <input type="text" name="no_faktur" class="form-control" placeholder="No Faktur" value="{{ request('no_faktur') }}">
                </div>
                <div class="col-md-2">
                  <input type="text" name="no_po" class="form-control" placeholder="No PO" value="{{ request('no_po') }}">
                </div>
                <div class="col-md-2">
                  <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>
                <p class="md-2">s/d</p>
                <div class="col-sm-2">
                  <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2">
                  <input type="text" name="pelanggan" class="form-control" placeholder="Nama Pelanggan" value="{{ request('pelanggan') }}">
                </div>
                <div class=" col-md-2">
                  <select name="status_pembayaran" class="form-control">
                    <option value="">- Status -</option>
                    <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Lunas" {{ request('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                  </select>
                </div>
                  <div class=" col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Reset</a>
                  </div>
              </div>
            </form>
              <table id="FakturTable" class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>NO</th>
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>No PO</th>
                        <th>Total</th>
                        <th>Status Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($penjualans as $index => $jual)
                      <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>{{ $jual->no_faktur }}</td>
                          <td>{{ $jual->tanggal }}</td>
                          <td>{{ $jual->pelanggan->nama ?? '-' }}</td>
                          <td>{{ $jual->no_po?? '-'  }}</td>
                          <td>Rp {{ number_format($jual->total, 0, ',', '.') }}</td>
                          <td>
                              @if ($jual->status_pembayaran == 'Lunas')
                                  <span class="badge badge-success">Lunas</span>
                                  @php
                                    // fallback ke approved_at kalau paid_date belum ada (data lama)
                                    $tanggalTampil = $jual->paid_date
                                                    ? \Carbon\Carbon::parse($jual->paid_date)
                                                    : ($jual->approved_at ? $jual->approved_at : null);
                                  @endphp
                                  @if($tanggalTampil)
                                    <div><small class="text-muted">({{ $tanggalTampil->format('d/m/Y') }})</small></div>
                                  @endif
                              @else
                                  <span class="badge badge-warning">Belum Lunas</span>
                              @endif
                          </td>
                          <td>
                          @if ($jual->status == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                          @else
                            <span class="badge badge-danger">Dibatalkan</span>
                          @endif
                        </td>
                          <td>
                            <div class="dropdown ">
                              <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-bars"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                              <a href="{{ route('penjualan.show', $jual->id) }}" class="dropdown-item text-info">
                                  <i class="fa fa-eye"></i> Lihat
                              </a>
                              <a href="{{ route('sales.sales_invoices.surat-jalan', $jual->id) }}" class="dropdown-item text-secondary">
                                <i class="fas fa-file-alt"></i> Surat Jalan
                              </a>
                              
                              @if($jual->status_pembayaran == 'Belum Lunas'&& $jual->status != 'batal')
                              <a href="" class="dropdown-item text-success" data-toggle="modal" data-target="#modalApprove{{ $jual->id }}"><i class="far fa-money-bill-alt"></i> Approve</a>
                              @endif

                              @if($jual->status_pembayaran === 'Lunas')
                                @php
                                    $batasMenit = 60 *24;
                                    $canCancel = false;

                                    if (!empty($jual->approved_at)) {
                                        $approvedAt = \Carbon\Carbon::parse($jual->approved_at); 
                                        $selisih = $approvedAt->diffInMinutes(now());
                                        $sisaMenit = max(0, $batasMenit - $selisih);
                                        $canCancel = $selisih <= $batasMenit;
                                    }
                                @endphp
                                @if($canCancel)
                                  <a href="#" class="dropdown-item text-danger open-unapprove" data-toggle="modal" data-target="#modalUnapprove{{ $jual->id }}">
                                    <i class="far fa-money-bill-alt"></i> Unapprove
                                  </a>
                                @endif
                              @endif

                              @if ($jual->status != 'batal'||$jual->status_pembayaran == 'Lunas')
                                <a href="{{ route('penjualan.edit', $jual->id) }}" class="dropdown-item text-primary"><i class="fa fa-edit"></i> Edit</a>
                              @endif

                              @if ($jual->status == 'aktif')
                                <form id="form-batal-{{ $jual->id }}" action="{{ route('penjualan.batal', $jual->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  @method('PUT')
                                  <button type="button" class="dropdown-item text-danger btn-batal" data-id="{{ $jual->id }}" ><i class="fas fa-times-circle"></i> Batal</button>
                                </form>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <!-- Modal Unapprove -->
                      @push('modals')
                      <div class="modal fade" id="modalUnapprove{{ $jual->id }}"tabindex="-1">
                        <div class="modal-dialog">
                          <form method="POST" action="{{ route('penjualan.unapprove', $jual->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pembatalan Lunas</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">
                                Pembatalan pembayaran invoice <strong>{{ $jual->no_faktur }}</strong> <br>
                                dengan total <strong>{{ rupiah($jual->total) }}</strong><br><br>
                                <small class="text-muted">
                                    *Batas pembatalan hanya 24 jam sejak approve.
                                </small>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                      @endpush

                    <!-- Modal -->
                    <div class="modal fade" id="modalApprove{{ $jual->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <form method="POST" action="{{ route('penjualan.approve', $jual->id) }}">
                          @csrf
                          @method('PUT')
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Konfirmasi Pelunasan</h5>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                              Konfirmasi pembayaran invoice <strong>{{ $jual->no_faktur }}</strong> <br>
                              dengan total <strong>{{ rupiah($jual->total) }}</strong>
                              <hr>
                              <div class="form-group">
                              <label for="paid_date_{{ $jual->id }}">Tanggal Pelunasan</label>
                              <input type="date" name="paid_date" id="paid_date_{{ $jual->id }}" class="form-control" value="{{ old('paid_date',now()->format('Y-m-d')) }}" required>
                              <small class="text-muted">Tanggal ini akan tersimpan sebagai tanggal pelunasan (Lunas).</small>
                            </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Ya, Tandai Lunas</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                    
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
  </div>
</section>
</div>
</div>
<script>
  $(document).ready(function() {
    $('#FakturTable').DataTable({
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
  $(document).on('show.bs.modal', '.modal', function () {
    const inputDate = $(this).find('input[name="approved_at"]');
    if (inputDate.length && !inputDate.val()) {
      const today = new Date().toISOString().slice(0,10);
      inputDate.val(today);
    }
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-batal').forEach(function (btn) {
        btn.addEventListener('click', function () {
          // e.preventDefault();
            let id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Yakin ingin membatalkan?',
                text: "Faktur yang dibatalkan tidak bisa dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-batal-' + id).submit();
                }
            });
        });
    });
});
</script>
@endsection