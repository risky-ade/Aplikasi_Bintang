@extends('layouts.main')
@section('content')
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
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
    <div class="row">
      <div class="col-12">
      {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif --}}
      <div class="card">
        <div class="card-header">
          <form method="GET" action="{{ route('histori-harga-beli.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                  <label>Tanggal Awal</label>
                  <input type="date" name="tanggal_awal" class="form-control"
                        value="{{ request('tanggal_awal') }}">
              </div>
              <div class="col-md-2">
                  <label>Tanggal Akhir</label>
                  <input type="date" name="tanggal_akhir" class="form-control"
                        value="{{ request('tanggal_akhir') }}">
              </div>
            <div class="col-md-3">
                  <label>Produk</label>
                  <select name="produk_id" id="produk_id" class="form-control">
                      <option value="">Semua Produk</option>
                      @foreach($produk as $p)
                          <option value="{{ $p->id }}"
                              {{ request('produk_id') == $p->id ? 'selected' : '' }}>
                              {{ $p->nama_produk }}
                          </option>
                      @endforeach
                  </select>
              </div>
            <div class="col-md-3">
              <label>Pemasok</label>
                <input type="text" name="pemasok" class="form-control" placeholder="Nama Pemasok" value="{{ request('pemasok') }}">
            </div>
            <div class="col-md-2 d-flex">
            <button type="submit" class="btn btn-primary">Filter</button>
            <div class="col-md-2">
            <a href="{{ route('histori-harga-beli.index') }}" class="btn btn-secondary">Reset</a>
            </div>
          </div>
          </form>
        </div>

        <div class="card-body ">
          <div class="d-flex justify-content-between mb-3">
            <form action="{{ route('histori-harga-beli.destroy-by-date') }}" method="POST" class="form-delete-date">
              @csrf
              @method('DELETE')
              <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
              <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
              <input type="hidden" name="produk_id" value="{{ request('produk_id') }}">
              <input type="hidden" name="pemasok" value="{{ request('pemasok') }}">
              <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Hapus Sesuai Rentang Tanggal
              </button>
            </form>
            <button type="submit" form="form-delete-selected" class="btn btn-danger btn-sm">
              <i class="fas fa-check-square"></i> Hapus Data Dipilih
            </button>
          </div>
          <form action="{{ route('histori-harga-beli.destroy-selected') }}" method="POST" id="form-delete-selected" class="form-delete-selected">
            @csrf
            @method('DELETE')
          <div class="table-responsive">
          <table id="HistoriTable" class="table table-bordered table-striped table-hover w-100 ">
            <thead class="bg-secondary text-white">
              <tr>
                <th><input type="checkbox" id="check-all"></th>
                <th>#</th>
                <th>Tanggal Faktur</th>
                <th>Produk</th>
                <th>Harga Beli Dasar</th>
                <th>Harga Baru</th>
                <th>Pemasok</th>
                <th>Keterangan</th>
                <th>Waktu</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($histori as $index => $row)
                <tr>
                  <td class="text-center">
                    <input type="checkbox" name="histori_ids[]" value="{{ $row->id }}" class="row-check">
                  </td>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $row->tanggal }}</td>
                  <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                  <td>Rp {{ number_format($row->harga_lama, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($row->harga_baru, 0, ',', '.') }}</td>
                  <td>{{ $row->pemasok->nama ?? '-' }}</td>
                  <td>{{ $row->keterangan ?? '-' }}</td>
                  <td>{{ $row->created_at->setTimezone('Asia/Jakarta')->format(' d-m-Y / H:i') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
          </div>
          </form>
        </div>
      </div>
      </div>
    </div>
    </div>
    </section>
  </div>
</div>
<script>
$(function () {
    $('#HistoriTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true,
        autoWidth: false,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Data kosong",
            infoFiltered: "(difilter dari _MAX_ data)",
            paginate: {
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        columnDefs: [
            { targets: [0,1,3,6,8], className: 'text-nowrap' },
            { targets: [0,2,4,5], className: 'text-center' },
            { targets: [0], orderable: false, searchable: false }
        ]
    });

    $('#check-all').on('change', function () {
        $('.row-check').prop('checked', this.checked);
    });

    $(document).on('change', '.row-check', function () {
        $('#check-all').prop('checked', $('.row-check:checked').length === $('.row-check').length);
    });

    $('.form-delete-selected').on('submit', function (e) {
        e.preventDefault();

        if ($('.row-check:checked').length === 0) {
            Swal.fire('Pilih data', 'Pilih minimal satu histori yang ingin dihapus.', 'warning');
            return;
        }

        const form = this;
        Swal.fire({
            title: 'Hapus histori terpilih?',
            text: 'Data histori yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    $('.form-delete-date').on('submit', function (e) {
        e.preventDefault();

        if (!$('[name="tanggal_awal"]', this).val() || !$('[name="tanggal_akhir"]', this).val()) {
            Swal.fire('Tanggal wajib diisi', 'Isi tanggal awal dan tanggal akhir terlebih dahulu, lalu filter data.', 'warning');
            return;
        }

        const form = this;
        Swal.fire({
            title: 'Hapus histori sesuai rentang tanggal?',
            text: 'Data histori dalam rentang tanggal dan filter aktif akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
<script>
$(function () {
    $('#produk_id').select2({
        placeholder: 'Pilih produk',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection
