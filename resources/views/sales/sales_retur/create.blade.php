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
            {{-- @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif --}}

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="penjualan_id">Pilih Faktur</label>
                <select name="penjualan_id" id="penjualan_id" class="form-control select2" required>

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
                    <th>diskon</th>
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
  // init select2 (biarkan seperti semula)
  $('#penjualan_id').select2({
    placeholder: 'Cari nomor faktur atau nama pelanggan',
    allowClear: true,
    ajax: {
      url: '{{ route("ajax.faktur-search") }}',
      dataType: 'json',
      delay: 250,
      data: params => ({ q: params.term }),
      processResults: data => ({ results: data }),
      cache: true
    }
  });

  // saat pilih faktur -> load detail
  $('#penjualan_id').on('change', function () {
    const id = $(this).val();
    if (!id) {
      $('#detail-penjualan').hide();
      $('#produk-retur-body').empty();
      return;
    }

    fetch(`/sales/sales_retur/get-detail/${id}`)
      .then(async res => {
        if (!res.ok) {
          const text = await res.text();
          throw new Error('HTTP '+res.status+': '+text);
        }
        return res.json();
      })
      .then(data => {
        const tbody = $('#produk-retur-body').empty();

        (data.details || []).forEach(item => {
          const nama  = (item.produk && item.produk.nama_produk) ? item.produk.nama_produk : '-';
          const pid   = (item.produk && item.produk.id) ? item.produk.id : '';
          const qty   = Number(item.qty || 0);
          const harga = Number(item.harga_jual || 0);
          const discU = Number(item.diskon_unit || 0);
          const discT = Number(item.diskon || 0);

          tbody.append(`
            <tr>
              <td>
                ${nama}
                <input type="hidden" name="produk_id[]" value="${pid}">
              </td>
              <td>${qty}</td>
              <td>Rp ${Math.round(harga).toLocaleString('id-ID')}</td>
              <td>Rp ${Math.round(discT).toLocaleString('id-ID')}</td>
              <td>
                <input type="number" name="qty_retur[]" class="form-control qty-retur"
                       data-harga="${harga}" data-discunit="${discU}"
                       min="0" max="${qty}" value="0">
              </td>
              <td class="subtotal">Rp 0</td>
            </tr>
          `);
        });

        $('#detail-penjualan').show();
      })
      .catch(err => {
        console.error(err);
        alert('Gagal memuat detail faktur.\n'+err.message);
        $('#detail-penjualan').hide();
        $('#produk-retur-body').empty();
      });
  });

  // hitung subtotal per baris: qty * (harga - diskon_unit)
  $(document).on('input', '.qty-retur', function () {
    const harga    = Number($(this).data('harga') || 0);
    const discUnit = Number($(this).data('discunit') || 0);
    const qty      = Number($(this).val() || 0);
    const netUnit  = Math.max(0, harga - discUnit);
    const sub      = Math.max(0, qty * netUnit);
    $(this).closest('tr').find('.subtotal').text('Rp ' + Math.round(sub).toLocaleString('id-ID'));
  });
});
</script>

@endsection