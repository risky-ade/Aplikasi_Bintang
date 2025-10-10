@extends('layouts.main')
@section('content')
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Faktur Pembelian</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Faktur Pembelian</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/purchases/purchase_inv/create">
                  <button class="btn btn-primary me-md-2" type="button"><i
                    class="fas fa-solid fa-plus"></i>Tambah Data</button>
                </a>
              </div>
            </div>
              <!-- /.card-header -->
            <div class="card-body">
              <form method="GET" action="{{ route('pembelian.index') }}" class="mb-3">
              <div class="row">
                <div class="col-md-2 mb-3">
                  <input type="text" name="no_faktur" class="form-control" placeholder="No Faktur" value="{{ request('no_faktur') }}">
                </div>
                {{-- <div class="col-md-2">
                  <input type="text" name="no_po" class="form-control" placeholder="No PO" value="{{ request('no_po') }}">
                </div> --}}
                <div class="col-md-2">
                  <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>
                <p class="md-2">s/d</p>
                <div class="col-sm-2">
                  <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2">
                  <input type="text" name="pemasok" class="form-control" placeholder="Nama Pemasok" value="{{ request('pemasok') }}">
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
                    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Reset</a>
                  </div>
              </div>
            </form>
              <table id="FakturTable" class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>NO</th>
                        <th>No Faktur</th>
                        <th>No PO/Nota</th>
                        <th>Tanggal</th>
                        <th>Pemasok</th>
                        <th>Total</th>
                        <th>Status Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($pembelians as $index => $beli)
                      <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>{{ $beli->no_faktur }}</td>
                          <td>{{ $beli->no_po?? '-'  }}</td>
                          <td>{{ $beli->tanggal }}</td>
                          <td>{{ $beli->pemasok->nama ?? '-' }}</td>
                          <td>Rp {{ number_format($beli->total, 0, ',', '.') }}</td>
                          <td>
                              @if ($beli->status_pembayaran == 'Lunas')
                                  <span class="badge badge-success">Lunas</span>
                                   @if($beli->approved_at)
                                    <div><small class="text-muted">({{ $beli->approved_at->format('d/m/Y') }})</small></div>
                                  @endif
                              @else
                                  <span class="badge badge-warning">Belum Lunas</span>
                              @endif
                          </td>
                          <td>
                          @if ($beli->status == 'aktif')
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
                              <a href="{{ route('pembelian.show', $beli->id) }}" class="dropdown-item text-info">
                                    <i class="fa fa-eye"></i> Lihat
                               </a>
                              <a href="{{ route('pembelian.edit',$beli->id) }}" class="dropdown-item text-primary"
                                        type="button"><i class="fa fa-edit"></i> Edit
                              </a>
                              {{-- <a href="{{ route('penjualan.print', $jual->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                                  <i class="fa fa-print"></i>
                              </a> --}}
                              {{-- <a href="{{ route('sales.sales_invoices.surat-jalan', $beli->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-file-alt"></i>
                              </a> --}}

                              <!-- Tombol -->
                              @if($beli->status_pembayaran == 'Belum Lunas'&& $beli->status != 'batal')
                              <a href="" class="dropdown-item text-success" data-toggle="modal" data-target="#modalApprove{{ $beli->id }}"><i class="far fa-money-bill-alt"></i> Approve</a>
                              @endif

                              @if($beli->status_pembayaran === 'Lunas')
                                @php
                                    $batasMenit = 60 *24;
                                    $canCancel = false;

                                    if (!empty($beli->approved_at)) {
                                        $approvedAt = \Carbon\Carbon::parse($beli->approved_at); 
                                        $selisih = $approvedAt->diffInMinutes(now());
                                        $sisaMenit = max(0, $batasMenit - $selisih);
                                        $canCancel = $selisih <= $batasMenit;
                                    }
                                @endphp
                                @if($canCancel)
                                  <a href="" class="dropdown-item text-danger" data-toggle="modal" data-target="#modalUnapprove{{ $beli->id }}">
                                    Unapprove
                                  </a>

                                    <!-- Modal Unapprove -->
                                <div class="modal fade" id="modalUnapprove{{ $beli->id }}"tabindex="-1">
                                  <div class="modal-dialog">
                                    {{-- <form method="POST" action="{{ route('penjualan.unapprove', $beli->id) }}"> --}}
                                      @csrf
                                      @method('PUT')
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title">Konfirmasi Pembatalan Lunas</h5>
                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                          Pembatalan pembayaran invoice <strong>{{ $beli->no_faktur }}</strong> <br>
                                          dengan total <strong>{{ rupiah($beli->total) }}</strong><br><br>
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
                                @endif
                              @endif


                              {{-- @if ($beli->status != 'batal'||$beli->status_pembayaran == 'Lunas')
                                <a href="{{ route('penjualan.edit', $beli->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                              @endif --}}

                              @if ($beli->status == 'aktif')
                                <form id="form-batal-{{ $beli->id }}" action="{{ route('pembelian.batal', $beli->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  @method('PUT')
                                  <button type="button" class="dropdown-item text-danger btn-batal" data-id="{{ $beli->id }}"><i class="fas fa-times-circle"></i> Batal</button>
                                </form>
                              @endif
                              </div>
                            </div>
                          </td>
                      </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="modalApprove{{ $beli->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <form method="POST" action="{{ route('pembelian.approve', $beli->id) }}">
                          @csrf
                          @method('PUT')
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Konfirmasi Pelunasan</h5>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                              Konfirmasi pembayaran invoice <strong>{{ $beli->no_faktur }}</strong> <br>
                              dengan total <strong>{{ rupiah($beli->total) }}</strong>
                              <hr>
                              <div class="form-group">
                              <label for="approved_at_{{ $beli->id }}">Tanggal Pelunasan</label>
                              <input type="date" name="approved_at" id="approved_at_{{ $beli->id }}" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
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