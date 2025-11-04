@extends('layouts.main')
@section('content')
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
<style>
.pagination {
    margin-top: 20px;
}
</style>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Histori Harga Pembelian</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Histori Harga Pembelian</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

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
          <form method="GET" action="{{ route('histori-harga-beli.index') }}" class="form-inline">
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
                <input type="text" name="pemasok" class="form-control" placeholder="Nama Pemasok" value="{{ request('pemasok') }}">
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
            <a href="{{ route('histori-harga-beli.index') }}" class="btn btn-secondary">Reset</a>
          </div>
          </form>
        </div>

        <div class="card-body table-responsive p-0">
          <table id="HistoriTable" class="table table-bordered table-striped table-hover w-100 nowrap">
            <thead class="bg-dark text-white">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Harga Beli Dasar</th>
                <th>Harga Baru</th>
                {{-- <th>Sumber</th> --}}
                <th>Pemasok</th>
                <th>Keterangan</th>
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
                  <td>{{ $row->pemasok->nama ?? '-' }}</td>
                  <td>{{ $row->keterangan ?? '-' }}</td>
                  <td>{{ $row->created_at->setTimezone('Asia/Jakarta')->format(' d-m-Y / H:i') }}</td>
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
  </div>
  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>
<script>
    $(document).ready(function() {
    $('#HistoriTable').DataTable({
      autoWidth: false,    
      responsive: false,    
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,1,2,3,4,5,6,7,8], className: 'text-nowrap' },
        // { targets: [3], width: '220px' }
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