@extends('layouts.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
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
                                            <th>Tanggal</th>
                                            <th>Pelanggan</th>
                                            <th>Total</th>
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
                                                <td>Rp {{ number_format($jual->total, 0, ',', '.') }}</td>
                                                <td>
                                                    {{-- <a href="{{ route('sales.sales_invoices.show', $jual->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i> Detail
                                                    </a>
                                                    <a href="{{ route('sales.sales_invoices.cetak', $jual->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                                                        <i class="fa fa-print"></i> Cetak
                                                    </a> --}}
                                                    {{-- <form action="{{ route('sales.sales_invoices.destroy', $jual->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>
                                                    </form> --}}
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