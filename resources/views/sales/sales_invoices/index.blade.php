@extends('layouts.main')
@section('content')

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
      @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
      @endif
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
                                          <td>{{ $jual->no_po }}</td>
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
                                              <a href="{{ route('sales.sales_invoices.surat-jalan', $jual->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                <i class="fas fa-file-alt"></i>
                                              </a>
                                              <form action="{{ route('penjualan.destroy', $jual->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?')" style="display:inline;">
                                                  @csrf
                                                  @method('DELETE')
                                                  <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                              </form>

                                          </td>
                                          {{-- <td class="text-center">
                                              <a href="{{ url('/master_produk/'.$row->id.'/edit') }}" class="btn btn-info"
                                                  type="button"><i class="fa fa-edit"></i> </a>
                                              <form action="{{ url('/master_produk/'.$row->id) }}" method="POST" style="display:inline">
                                                  @csrf @method('delete')
                                                  <button type="submit" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                              </form>
                                              <a href="{{ url('/delete') }}" class="btn btn-danger "type="button"><i class="fa fa-trash"></i> </a>
                                          </td> --}}
                                      </tr>
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
<!-- ./wrapper -->
@endsection