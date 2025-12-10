@extends('layouts.main')
@section('content')
<style>
  .nowrap th, .nowrap td { white-space: nowrap; }
</style>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Laporan Penjualan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Laporan Penjualan</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="card-body">
      <form method="GET" action="{{ route('sales_report.index') }}" class="mb-3">
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
            <label>Pelanggan</label>
            <select name="pelanggan_id" class="form-control">
              <option value="">-- Semua Pelanggan --</option>
              @foreach ($pelanggans as $pelanggan)
                <option value="{{ $pelanggan->id }}" {{ request('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                  {{ $pelanggan->nama }}
                </option>
              @endforeach
            </select>
          </div>
          <div class=" col-sm-2">
            <label>Pembayaran</label>
            <select name="status_pembayaran" class="form-control">
              <option value="">-- Status --</option>
              <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
              <option value="Belum Lunas" {{ request('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('sales_report.index') }}" class="btn btn-secondary mr-2">Reset</a>
            <a href="{{ route('sales_report.sales_pdf', request()->query()) }}" class="btn btn-danger">Simpan PDF</a>
          </div>
        </div>
      </form>

      <div class="card container-fluid">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover w-100 nowrap" id="laporanTable">
            <thead class="bg-secondary text-white">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Faktur</th>
                <th>Pelanggan</th>
                <th>Nomor PO</th>
                <th>Total Retur</th>
                <th>Total Netto</th>
                <th>Status Pembayaran</th>
              </tr>
            </thead>
            <tbody>
              @foreach($penjualans as $index => $penjualan)
                <tr ondblclick="window.location='{{ route('penjualan.show', $penjualan->id) }}'" style="cursor: pointer;" title="Double klik untuk lihat detail">
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $penjualan->tanggal }}</td>
                  <td>{{ $penjualan->no_faktur }}</td>
                  <td>{{ $penjualan->pelanggan->nama ?? '-' }}</td>
                  <td>{{ $penjualan->no_po ?? '-' }}</td>
                  <td>Rp {{ number_format($penjualan->total_retur ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format(($penjualan->total_netto_calc ?? $penjualan->total ?? 0), 0, ',', '.') }}</td>
                  <td>{{ ucfirst($penjualan->status_pembayaran) }}</td>
                </tr>
              @endforeach
            </tbody>
            <thead class="bg-secondary text-white">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Faktur</th>
                <th>Pelanggan</th>
                <th>Nomor PO</th>
                <th>Total Retur</th>
                <th>Total Netto</th>
                <th>Status Pembayaran</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="5" class="text-right">Total:</th>
                <th class="tot-col-5"></th>
                <th class="tot-col-6"></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
          </div>
        </div>
      </div>
      </div>
    </section>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#laporanTable').DataTable({
      autoWidth: false,    
      responsive: false,    
      pageLength: 10,
      lengthMenu: [10, 15, 25, 50, 100],
      columnDefs: [
        { targets: [0,1,2,4,5,6,7], className: 'text-nowrap' },
        { targets: [3], width: '220px' }
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
      footerCallback: function (row, data, start, end, display) {
        const api = this.api();

        function sumCol(idx) {
          return api.column(idx, { page: 'current' }).data().reduce(function (a, b) {
            const na = parseInt((a||'0').toString().replace(/[^\d]/g, ''), 10) || 0;
            const nb = parseInt((b||'0').toString().replace(/[^\d]/g, ''), 10) || 0;
            return na + nb;
          }, 0);
        }
        function fmtIDR(n) {
          return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n);
        }

        const totalRetur = sumCol(5);
        const totalNetto = sumCol(6);

        $(api.column(5).footer()).html(fmtIDR(totalRetur));
        $(api.column(6).footer()).html(fmtIDR(totalNetto));
      }
    });
  });
</script>
@endsection
