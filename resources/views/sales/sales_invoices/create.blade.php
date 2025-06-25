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
    <div class="content-wrapper">
        <section class="content p-3">
            <form method="POST" action="{{ route('penjualan.store') }}">
                @csrf

                <div class="form-group">
                    <label>No Faktur</label>
                    <input type="text" name="no_faktur" class="form-control" value="{{ $no_faktur }}" readonly>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                </div>

                <div class="form-group">
                    <label>Pelanggan</label>
                    <select name="pelanggan_id" class="form-control">
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <hr>
                <h5>Daftar Produk</h5>
                <table class="table table-bordered" id="produk-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th><button type="button" class="btn btn-success btn-sm" onclick="addRow()">+</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="produk_id[]" class="form-control">
                                    @foreach ($produk as $item)
                                        <option value="{{ $item->id }}" data-harga="{{ $item->harga_jual }}">{{ $item->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="qty[]" class="form-control qty" value="1"></td>
                            <td><input type="number" name="harga[]" class="form-control harga">{{ $item->harga_jual }}</td>
                            <td><input type="number" name="subtotal[]" class="form-control subtotal" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">×</button></td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group">
                    <label>Total</label>
                    <input type="number" id="grand_total" class="form-control" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </form>
        </section>
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
<script>
function addRow() {
    let row = $('#produk-table tbody tr:first').clone();
    row.find('input').val('');
    $('#produk-table tbody').append(row);
}

function removeRow(el) {
    if ($('#produk-table tbody tr').length > 1) {
        $(el).closest('tr').remove();
        hitungTotal();
    }
}

$(document).on('change', 'select[name="produk_id[]"]', function() {
    let harga = $(this).find(':selected').data('harga') || 0;
    let row = $(this).closest('tr');
    row.find('.harga').val(harga);
    hitungSubtotal(row);
});

$(document).on('input', '.qty', function() {
    let row = $(this).closest('tr');
    hitungSubtotal(row);
});

function hitungSubtotal(row) {
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let harga = parseFloat(row.find('.harga').val()) || 0;
    let subtotal = qty * harga;
    row.find('.subtotal').val(subtotal);
    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    $('.subtotal').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#grand_total').val(total);
}
</script>
@endsection
