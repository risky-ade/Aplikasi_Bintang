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
          <h1 class="m-0">Faktur Penjualan</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Faktur Penjualan</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
      {{-- @if (session()->has('success'))
          <div class="alert alert-success ">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
      @endif --}}
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
                      <!-- /.card-header -->
                      <div class="card-body">
                        <form method="GET" action="{{ route('penjualan.index') }}" class="mb-3">
                        <div class="row">
                          <div class="col-md-2">
                            <input type="text" name="no_faktur" class="form-control" placeholder="No Faktur" value="{{ request('no_faktur') }}">
                          </div>
                          <div class="col-md-2">
                            <input type="text" name="no_po" class="form-control" placeholder="No PO" value="{{ request('no_po') }}">
                          </div>
                          <div class="col-md-2">
                            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                          </div>
                          <div class="col-md-2">
                            <input type="text" name="pelanggan" class="form-control" placeholder="Nama Pelanggan" value="{{ request('pelanggan') }}">
                          </div>
                          <div class="col-md-2">
                            <select name="status_pembayaran" class="form-control">
                              <option value="">-- Status --</option>
                              <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                              <option value="Belum Lunas" {{ request('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Reset</a>
                          </div>
                        </div>
                      </form>
                          <table id="example2" class="table table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>NO</th>
                                      <th>No Faktur</th>
                                      <th>No PO</th>
                                      <th>Tanggal</th>
                                      <th>Pelanggan</th>
                                      <th>Total</th>
                                      <th>Status</th>
                                      <th>Aksi</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($penjualans as $index => $jual)
                                      <tr>
                                          <td>{{ $index + 1 }}</td>
                                          <td>{{ $jual->no_faktur }}</td>
                                          <td>{{ $jual->no_po?? '-'  }}</td>
                                          <td>{{ $jual->tanggal }}</td>
                                          <td>{{ $jual->pelanggan->nama ?? '-' }}</td>
                                          <td>Rp {{ number_format($jual->total, 0, ',', '.') }}</td>
                                          <td>
                                              @if ($jual->status_pembayaran == 'Lunas')
                                                  <span class="badge badge-success">Lunas</span>
                                              @else
                                                  <span class="badge badge-warning">Belum Lunas</span>
                                              @endif
                                          </td>
                                          <td>
                                            <a href="{{ route('penjualan.edit',$jual->id) }}" class="btn btn-info btn-sm"
                                                      type="button"><i class="fa fa-edit"></i> 
                                            </a>
                                              <a href="{{ route('penjualan.show', $jual->id) }}" class="btn btn-info btn-sm">
                                                  <i class="fa fa-eye"></i>
                                              </a>
                                              <a href="{{ route('penjualan.print', $jual->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                                                  <i class="fa fa-print"></i>
                                              </a>
                                              <a href="{{ route('sales.sales_invoices.surat-jalan', $jual->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-file-alt"></i>
                                              </a>

                                              <!-- Tombol -->
                                              @if($jual->status_pembayaran == 'Belum Lunas')
                                              <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalApprove{{ $jual->id }}">Approve</button>
                                              @endif

                                              @if($jual->status_pembayaran === 'Lunas')
                                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalUnapprove{{ $jual->id }}">Batalkan Lunas</button>
                                              @endif

                                              @if ($jual->status_pembayaran !== 'Lunas')
                                              <form action="{{ route('penjualan.destroy', $jual->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?')" style="display:inline;">
                                                  @csrf
                                                  @method('DELETE')
                                                  <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                              </form>
                                              @else
                                                {{-- <button class="btn btn-secondary btn-sm" disabled title="Sudah lunas">Edit</button> --}}
                                                <button class="btn btn-danger btn-sm" disabled title="Tidak bisa hapus karena sudah lunas"><i class="fas fa-trash"></i></button>
                                              @endif

                                          </td>
                                      </tr>

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
                                            </div>
                                            <div class="modal-footer">
                                              <button type="submit" class="btn btn-success">Ya, Tandai Lunas</button>
                                            </div>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                    <!-- Modal Unapprove -->
                                    <div class="modal fade" id="modalUnapprove{{ $jual->id }}" tabindex="-1">
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
                                              dengan total <strong>{{ rupiah($jual->total) }}</strong>
                                            </div>
                                            <div class="modal-footer">
                                              <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                            </div>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                      <!-- /.card-body -->
                  </div>
                      <!-- /.card -->
              </div>
          </div>
      </div>
    </section>

  <!-- /.content -->
</div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2024-2025 <a href="#">CV.Bintang Empat</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>

@endsection