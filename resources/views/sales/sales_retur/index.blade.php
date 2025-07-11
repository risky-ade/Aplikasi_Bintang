@extends('layouts.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Retur Penjualan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active">Retur Penjualan</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

      <div class="card">
        <div class="card-header d-flex justify-content-end">
          <a href="{{ route('retur-penjualan.create') }}" class="btn btn-primary">
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
                <th>Pelanggan</th>
                <th>Total Retur</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($returs as $index => $retur)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $retur->no_retur }}</td>
                  <td>{{ $retur->penjualan->no_faktur ?? '-' }}</td>
                  <td>{{ $retur->tanggal_retur }}</td>
                  <td>{{ $retur->penjualan->pelanggan->nama ?? '-' }}</td>
                  <td>Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                  <td>
                    {{-- <a href="{{ route('retur-penjualan.show', $retur->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a> --}}
                    <form action="{{ route('retur-penjualan.destroy', $retur->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus retur ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
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