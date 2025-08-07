@extends('layouts.main')
@section('content')
<style>
.pagination {
    margin-top: 20px;
}
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Histori Harga Penjualan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Histori Harga Penjualan</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
<div class="container-fluid">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
          <form method="GET" action="{{ route('histori-harga.index') }}" class="form-inline">
            <div class="form-group mr-2">
              <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
            </div>
            <div class="form-group mr-2">
              <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
            </div>
            <div class="form-group mr-2">
              <select name="produk_id" class="form-control">
                <option value="">-- Semua Produk --</option>
                @foreach($produk as $p)
                  <option value="{{ $p->id }}" {{ request('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group mr-2">
                <input type="text" name="pelanggan" class="form-control" placeholder="Nama Pelanggan" value="{{ request('pelanggan') }}">
            </div>
            {{-- <div class="form-group mr-2">
              <select name="sumber" class="form-control">
                <option value="">-- Semua Sumber --</option>
                <option value="produk" {{ request('sumber') == 'produk' ? 'selected' : '' }}>Master Produk</option>
                <option value="penjualan" {{ request('sumber') == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
              </select>
            </div> --}}
            <button type="submit" class="btn btn-primary">Filter</button>
            <div class="col-md-2">
            <a href="{{ route('histori-harga.index') }}" class="btn btn-secondary">Reset</a>
          </div>
          </form>
        </div>

        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-hover table-striped">
            <thead class="bg-dark text-white">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Harga Lama</th>
                <th>Harga Baru</th>
                {{-- <th>Sumber</th> --}}
                {{-- <th>Keterangan</th> --}}
                <th>Pelanggan</th>
                <th>Waktu</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($histori as $index => $row)
                <tr>
                  <td>{{ $histori->firstItem() + $index }}</td>
                  <td>{{ $row->tanggal }}</td>
                  <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                  <td>Rp {{ number_format($row->harga_lama, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($row->harga_baru, 0, ',', '.') }}</td>
                  {{-- <td>
                    @if($row->sumber == 'produk')
                      <span class="badge badge-info">Master Produk</span>
                    @elseif($row->sumber == 'penjualan')
                      <span class="badge badge-success">Penjualan</span>
                    @else
                      <span class="badge badge-secondary">{{ $row->sumber }}</span>
                    @endif
                  </td> --}}
                  {{-- <td>{{ $row->keterangan ?? '-' }}</td> --}}
                  <td>{{ $row->pelanggan->nama ?? '-' }}</td>
                  <td>{{ $row->created_at->setTimezone('Asia/Jakarta')->format(' H:i') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">Belum ada histori harga.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer">
          {{ $histori->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@endsection