@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Produk Hilang</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Produk Hilang</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      {{-- @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif --}}
      <div class="card">
        <div class="card-header text-right">
          <a href="{{ route('product_losses.create') }}" class="btn btn-primary btn-sm">Tambah Produk Hilang</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="DataTable">
              <thead class="bg-secondary text-white">
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Keterangan</th>
                  <th>User</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($losses as $loss)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $loss->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $loss->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $loss->qty }}</td>
                    <td>{{ $loss->keterangan ?? '-' }}</td>
                    <td>{{ $loss->user->name ?? '-' }}</td>
                    <td>
                      <a href="{{ route('product_losses.show', $loss->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="{{ route('product_losses.edit', $loss->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                      </a>
                      <form action="{{ route('product_losses.destroy', $loss->id) }}" method="POST" class="d-inline form-delete-loss">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
$(function () {
  $('#DataTable').DataTable();

  $(document).on('submit', '.form-delete-loss', function(e) {
    e.preventDefault();

    if (confirm('Hapus data produk hilang ini? Stok produk akan dikoreksi ulang.')) {
      this.submit();
    }
  });
});
</script>
@endsection
