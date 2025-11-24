@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><h3>Faktur Pembelian</h3></div></div>
<style>
    thead tr {
        background-color: #001f3f; 
        color: white;
    }

    .form-control {
        height: 36px;
        padding: 0.25rem 0.5rem;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }

    .produk-column {
        min-width: 200px;
    }

    .number-input {
        text-align: right;
    }
</style>
  <section class="content">
    <form method="POST" action="{{ route('pembelian.store') }}">
      @csrf
      <div class="card">
        <div class="card-body">
          @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

          <div class="row mb-3">
            <div class="col-md-3">
                <label>No Faktur</label>
                <input type="text" name="no_faktur" class="form-control" value="{{ $no_faktur }}" readonly>
            </div>
            <div class="col-md-3">
              <label>Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
              <label>Pemasok</label>
              <select name="pemasok_id" class="form-control" required>
                <option value="">-- Pilih Pemasok --</option>
                @foreach($pemasok as $s)
                  <option value="{{ $s->id }}">{{ $s->nama }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label>No PO/Nota</label>
              <input type="text" name="no_po" class="form-control" placeholder="Opsional">
            </div>
            <div class="col-md-3">
              <label>Status Pembayaran</label>
              <select name="status_pembayaran" class="form-control" required>
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
          <table class="table table-bordered">
            <thead class="bg-secondary text-white">
              <tr>
                <th style="min-width:220px">Produk</th>
                <th>Qty</th>
                <th>Harga Beli</th>
                <th>Diskon</th>
                <th>Subtotal</th>
                <th>
                  <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+</button>
                </th>
              </tr>
            </thead>
            <tbody id="produk-body">
              <tr>
                <td>
                  <select name="produk_id[]" class="form-control produk-select" required></select>
                </td>
                <td><input type="number" name="qty[]" class="form-control qty number-input" min="1" required></td>
                <td>
                    <input type="hidden" name="harga_beli[]" class="harga">
                    <input type="text" class="form-control harga_display number-input" required>
                </td>
                <td>
                    <input type="hidden" name="diskon[]" class="diskon">
                    <input type="text" class="form-control diskon_display number-input" min="0" value="0">
                </td>
                <td>
                    <input type="hidden" name="subtotal[]" class="subtotal">
                    <input type="text" class="form-control subtotal_display number-input" readonly>
                </td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button></td>
              </tr>
            </tbody>
          </table>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label>Catatan</label>
              <textarea name="catatan" rows="5" class="form-control" placeholder="Opsional..."></textarea>
            </div>
            <div class="col-md-6">
              <table class="table table-bordered">
                <tr>
                  <th>Subtotal</th>
                  <td>
                    <input type="hidden" name="total_subtotal" class="total_subtotal">
                    <input type="text" class="form-control total_subtotal_display number-input" readonly>
                  </td>
                </tr>
                <tr>
                  <th>Diskon Nota</th>
                  <td>
                    <input type="hidden" name="diskon_nota" class="diskon_nota">
                    <input type="text" class="form-control diskon_nota_display number-input" value="0">
                  </td>
                </tr>
                <tr>
                  <th>PPN / Pajak (%)</th>
                  <td><input type="number" name="pajak" class="form-control number-input" value="0" min="0"></td>
                </tr>
                <tr>
                  <th>Biaya Kirim</th>
                  <td>
                    <input type="hidden" name="biaya_kirim" class="biaya_kirim">
                    <input type="text" class="form-control biaya_kirim_display number-input " value="0">
                  </td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td>
                    <input type="hidden" name="total" class="total">
                    <input type="text" class="form-control total_display number-input " readonly>
                  </td>
                </tr>
              </table>
            </div>
          </div>

          <div class="text-right">
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <button class="btn btn-sm btn-primary">Simpan Pembelian</button>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>

<template id="row-template">
<tr>
  <td><select name="produk_id[]" class="form-control produk-select" required></select></td>
  <td><input type="number" name="qty[]" class="form-control qty number-input" min="1" required></td>
  <td>
    <input type="hidden" name="harga_beli[]" class="harga">
    <input type="text" class="form-control harga_display number-input" required>
  </td>
  <td>
    <input type="hidden" name="diskon[]" class="diskon">
    <input type="text" class="form-control diskon_display number-input" min="0" value="0">
  </td>
  <td>
    <input type="hidden" name="subtotal[]" class="subtotal">
    <input type="text" class="form-control subtotal_display number-input" readonly>
  </td>
  <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button></td>
</tr>
</template>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  function initSelect2(ctx = document) {
    $(ctx).find('.produk-select').select2({
      placeholder: 'Cari Produk…',
      ajax: {
        url: '{{ route("produk.search") }}',
        dataType: 'json',
        delay: 250,
        processResults: data => ({ results: data.results })
      }
    }).on('select2:select', function(e){
      const data = e.params.data;
      const row = $(this).closest('tr');
      row.find('.harga').val(data.harga_dasar ?? 0);
      row.find('.harga_display').val('Rp ' + data.harga_dasar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
      row.find('.qty').val(1).trigger('input');
    });
  }

  function tambahBaris(){
    const tpl = document.getElementById('row-template').content.cloneNode(true);
    $('#produk-body').append(tpl);
    initSelect2($('#produk-body tr:last'));
  }
  function hapusBaris(btn){
    if ($('#produk-body tr').length > 1) {
      $(btn).closest('tr').remove();
      hitungTotal();
    }
  }
  function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function hitungTotal() {
    let total = 0;
    let totalDiskon = 0;

    $('#produk-body tr').each(function () {
        const qty = parseFloat($(this).find('.qty').val()) || 0;
        const harga = parseFloat($(this).find('.harga').val()) || 0; 
        const diskon = parseFloat($(this).find('.diskon').val()) || 0; 
        const subtotal = (qty * harga) - (qty * diskon);

        $(this).find('.diskon').val(diskon);
        $(this).find('.diskon_display').val(formatRupiah(diskon));
        $(this).find('.subtotal').val(subtotal);
        $(this).find('.subtotal_display').val(formatRupiah(subtotal));

        total += subtotal;
        totalDiskon += diskon;
    });

    const pajak = parseFloat($('[name="pajak"]').val()) || 0;
    const biayaKirim = parseFloat($('.biaya_kirim').val()) || 0;
    const diskonNota  = parseFloat($('.diskon_nota').val()) || 0;

    const subtotDisc = Math.max(0, total - diskonNota);
    const totalPajak = (subtotDisc * pajak) / 100;
    const grandTotal = subtotDisc + totalPajak + biayaKirim;

  
    $('[name="total_subtotal"]').val(total);
    $('[name="diskon_nota"]').val(diskonNota);
    $('[name="biaya_kirim"]').val(biayaKirim);
    $('[name="total"]').val(grandTotal);

 
    $('.total_subtotal_display').val(formatRupiah(total));
    $('.diskon_nota_display').val(formatRupiah(diskonNota));
    $('.biaya_kirim_display').val(formatRupiah(biayaKirim));
    $('.total_display').val(formatRupiah(grandTotal));
}

$(document).ready(function () {
    initSelect2();
    hitungTotal();

    // trigger total setiap input berubah
    $(document).on('input', '.qty, .harga_display, .diskon_display, .biaya_kirim_display, [name="pajak"], .diskon_nota_display', function () {
        // konversi display ke hidden murni dulu
        if ($(this).hasClass('harga_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).closest('tr').find('.harga').val(value);
        }
        if ($(this).hasClass('diskon_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).closest('tr').find('.diskon').val(value);
        }
        if ($(this).hasClass('diskon_nota_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $('.diskon_nota').val(value);
        }
        if ($(this).hasClass('biaya_kirim_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $('.biaya_kirim').val(value);
        }

        hitungTotal();
    });
});
  
</script>
@endsection