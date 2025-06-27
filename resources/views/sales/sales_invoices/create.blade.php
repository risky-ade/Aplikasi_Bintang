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
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Faktur Penjualan</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
<section class="content">
        @if (session('error'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

    {{-- <div class="content-wrapper"> --}}
        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <!-- Informasi Penjualan -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>No Faktur</label>
                            <input type="text" name="no_faktur" class="form-control" value="{{ $no_faktur }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label>Pelanggan</label>
                            <select name="pelanggan_id" class="form-control" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach ($pelanggan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tambahan Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Biaya Pengiriman</label>
                            <input type="number" name="biaya_kirim" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label>Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" class="form-control" required>
                        </div>
                    </div>

                    <!-- Tabel Produk -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="produk-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga Jual</th>
                                    <th>Diskon</th>
                                    <th>Subtotal</th>
                                    <th>
                                        <button type="button" class="btn btn-sm btn-success" onclick="tambahProduk()">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="produk-body">
                                <tr>
                                    <td>
                                        <select name="produk_id[]" class="form-control produk-select w-100" required></select>
                                    </td>
                                    <td><input type="number" name="qty[]" class="form-control qty" value="1" required></td>
                                    <td><input type="number" name="harga_jual[]" class="form-control harga" required></td>
                                    <td><input type="number" name="diskon[]" class="form-control diskon" value="0"></td>
                                    <td><input type="text" class="form-control subtotal" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total -->
                    <div class="form-group text-right">
                        <label><strong>Total Bayar:</strong></label>
                        <input type="text" name="total_bayar" class="form-control text-right" id="total_bayar" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </div>
        </form>
    {{-- </div> --}}
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

<script>
function initSelect2() {
    $('.produk-select').select2({
        placeholder: 'Cari Produk...',
        ajax: {
            url: '{{ route("produk.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            }
        }
    }).on('select2:select', function (e) {
        const data = e.params.data;
        const row = $(this).closest('tr');
        row.find('.harga').val(data.harga_jual);
        row.find('.qty').val(1).trigger('input');
    });
}

function tambahProduk() {
    let row = `
    <tr>
        <td>
            <select name="produk_id[]" class="form-control produk-select" required></select>
        </td>
        <td><input type="number" name="qty[]" class="form-control qty" value="1" required></td>
        <td><input type="number" name="harga_jual[]" class="form-control harga" required></td>
        <td><input type="number" name="diskon[]" class="form-control diskon" value="0"></td>
        <td><input type="text" class="form-control subtotal" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">X</button></td>
    </tr>`;
    $('#produk-body').append(row);
    initSelect2();
}

$(document).ready(function () {
    initSelect2();

    // Kalkulasi
    $('body').on('input', '.qty, .harga, .diskon', function () {
        const row = $(this).closest('tr');
        const qty = parseFloat(row.find('.qty').val()) || 0;
        const harga = parseFloat(row.find('.harga').val()) || 0;
        const diskon = parseFloat(row.find('.diskon').val()) || 0;
        const subtotal = (qty * harga) - diskon;
        row.find('.subtotal').val(subtotal.toFixed(2));
        hitungTotal();
    });

    $('input[name="biaya_kirim"]').on('input', function () {
        hitungTotal();
    });
});

function hapusBaris(el) {
    $(el).closest('tr').remove();
    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    $('.subtotal').each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    let biaya = parseFloat($('input[name="biaya_kirim"]').val()) || 0;
    total += biaya;

    $('#total_bayar').val(total.toFixed(2));
}
</script>

@endsection
