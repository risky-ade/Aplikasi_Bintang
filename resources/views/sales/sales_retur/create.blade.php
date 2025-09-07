@extends('layouts.main')
@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3 class="mb-3">Form Retur Penjualan</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <form method="POST" action="{{ route('retur-penjualan.store') }}">
        @csrf
        <div class="card">
          <div class="card-body">
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="penjualan_id">Pilih Faktur</label>
                <select name="penjualan_id" id="penjualan_id" class="form-control select2" required>
                  {{-- <option value="">-- Pilih Faktur --</option>
                  @foreach($penjualans as $penjualan)
                    <option value="{{ $penjualan->id }}">
                      {{ $penjualan->no_faktur }} - {{ $penjualan->pelanggan->nama ?? '' }}
                    </option>
                  @endforeach --}}
                </select>
              </div>
              <div class="col-md-6">
                <label for="tanggal_retur">Tanggal Retur</label>
                <input type="date" name="tanggal_retur" class="form-control" required value="{{ date('Y-m-d') }}">
              </div>
            </div>

            <div class="mb-3">
              <label>Alasan Retur</label>
              <textarea name="alasan" class="form-control" rows="2" placeholder="Opsional..."></textarea>
            </div>

            <div id="detail-penjualan" style="display: none;">
              <h5 class="mb-3">Detail Produk Penjualan</h5>
              <table class="table table-bordered">
                <thead class="bg-secondary text-white">
                  <tr>
                    <th>Produk</th>
                    <th>Qty Jual</th>
                    <th>Harga Jual</th>
                    <th>Qty Retur</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody id="produk-retur-body">
                  {{-- diisi lewat JS --}}
                </tbody>
              </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Retur</button>
            <a href="{{ route('retur-penjualan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    // Inisialisasi select2 AJAX
$('#penjualan_id').select2({
    placeholder: 'Cari nomor faktur atau nama pelanggan',
    allowClear: true,
    ajax: {
      url: '{{ route("ajax.faktur-search") }}',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });

  $('#penjualan_id').on('change', function () {
    const id = $(this).val();
    if (id) {
      fetch(`get-detail/${id}`)
        .then(res => res.json())
        .then(data => {
            console.log('data', data);
          const tbody = $('#produk-retur-body').empty();
          data.detail.forEach((item, index) => {
            tbody.append(`
              <tr>
                <td>
                  ${item.produk.nama_produk}
                  <input type="hidden" name="produk_id[]" value="${item.produk.id}">
                  <input type="hidden" name="harga_jual[]" value="${item.harga_jual}">
                </td>
                <td>${item.qty}</td>
                <td>Rp ${parseInt(item.harga_jual).toLocaleString()}</td>
                <td>
                  <input type="number" name="qty_retur[]" class="form-control qty-retur" data-harga="${item.harga_jual}" min="0" max="${item.qty}" value="0">
                </td>
                <td class="subtotal">Rp 0</td>
              </tr>
            `);
          });
          $('#detail-penjualan').show();
        });
    } else {
      $('#detail-penjualan').hide();
      $('#produk-retur-body').empty();
    }
  });

  $(document).on('input', '.qty-retur', function () {
    const harga = $(this).data('harga');
    const qty = parseInt($(this).val()) || 0;
    const subtotal = qty * harga;
    $(this).closest('tr').find('.subtotal').text('Rp ' + subtotal.toLocaleString());
  });
});
</script>

@endsection