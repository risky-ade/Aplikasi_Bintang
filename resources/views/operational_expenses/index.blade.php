@extends('layouts.main')
@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Biaya Operasional</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Biaya Operasional</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif --}}

      <div class="card">
        <div class="card-body">
          <form method="GET" action="{{ route('operational_expenses.index') }}" class="mb-3">
            <div class="row">
              <div class="col-md-2">
                <label>Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
              </div>
              <div class="col-md-2">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
              </div>
              <div class="col-md-3">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" value="{{ request('kategori') }}" placeholder="Contoh: transport">
              </div>
              <div class="col-md-5 d-flex align-items-end justify-content-end">
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('operational_expenses.index') }}" class="btn btn-secondary mr-2">Reset</a>
                <a href="{{ route('operational_expenses.create') }}" class="btn btn-primary"><i
                    class="fas fa-solid fa-plus"></i> Tambah</a>
              </div>
            </div>
          </form>

          <div class="mb-3">
            <strong>Total Biaya:</strong> {{ $rupiah($total) }}
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover w-100 nowrap" id="expensesTable">
              <thead class="bg-secondary text-white">
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Kategori</th>
                  <th>Keterangan</th>
                  <th>Nominal</th>
                  <th>User</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($expenses as $expense)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $expense->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $expense->kategori }}</td>
                    <td>{{ $expense->keterangan ?? '-' }}</td>
                    <td>{{ $rupiah($expense->nominal) }}</td>
                    <td>{{ $expense->user->name ?? '-' }}</td>
                    <td class="text-nowrap">
                      <a href="{{ route('operational_expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                      <form action="{{ route('operational_expenses.destroy', $expense->id) }}" method="POST" class="d-inline form-delete-expense">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
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
  $(document).ready(function() {
    $('#expensesTable').DataTable({
      autoWidth: false,
      responsive: false,
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ baris per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari total _MAX_ data)",
        paginate: { next: "Berikutnya", previous: "Sebelumnya" }
      }
    });
  });

  $(document).on('submit', '.form-delete-expense', function(e) {
    e.preventDefault();
    const form = this;

    Swal.fire({
      title: 'Hapus biaya operasional?',
      text: 'Data yang dihapus tidak dapat dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
</script>
@endsection
