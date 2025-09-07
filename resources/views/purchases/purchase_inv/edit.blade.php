@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><h3>Edit Faktur Pembelian</h3></div></div>
<style>
    thead tr {
        background-color: #001f3f; /* biru navy */
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
    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card">
        <div class="card-body">
          @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

          <div class="row mb-3">
            <div class="col-md-3">
                <label>No Faktur</label>
                <input type="text" name="no_faktur" class="form-control" value="{{ $pembelian->no_faktur }}" readonly>
            </div>
            <div class="col-md-3">
              <label>Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="{{ $pembelian->tanggal }}" required>
            </div>
            <div class="col-md-3">
              <label>Pemasok</label>
              <select name="pemasok_id" class="form-control" required>
                <option value="">-- Pilih Pemasok --</option>
                @foreach($pemasok as $s)
                  <option value="{{ $s->id }}"{{ $pembelian->pemasok_id == $s->id? 'selected' : ''}}>{{ $s->nama }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label>No PO/Nota</label>
              <input type="text" name="no_po" class="form-control" value="{{ $pembelian->no_po }}" >
            </div>
            <div class="col-md-3">
              <label>Status Pembayaran</label>
              <select name="status_pembayaran" class="form-control" required>
                <option value={{ $pembelian->status_pembayaran =='Belum Lunas'? 'selected' : ''}}>Belum Lunas</option>
                <option value={{$pembelian->status_pembayaran =='Lunas'? 'selected' : ''}}>Lunas</option>
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
                @foreach ($pembelian->detail as $detail)
              <tr>
                <td>
                    <select name="produk_id[]" class="form-control produk-select" required>
                        <option value="{{ $detail->master_produk_id }}" selected>{{ $detail->produk->nama_produk }}</option>
                    </select>
                    </td>
                    <td>
                    <input type="number" name="qty[]" value="{{ $detail->qty }}" class="form-control qty" required>
                    </td>
                    <td><input type="text" name="harga_beli[]" value="{{ $detail->harga_beli }}" class="form-control harga currency-input" required ></td>
                    <td><input type="number" name="diskon[]" value="{{ $detail->diskon }}" class="form-control diskon currency-input"></td>
                    <td><input type="number" name="subtotal[]" value="{{ $detail->subtotal }}" class="form-control subtotal currency-input" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">x</button></td>
              </tr>
              @endforeach
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
                  <td><input type="number" name="total_subtotal" class="form-control number-input" readonly></td>
                </tr>
                <tr>
                  <th>PPN / Pajak (%)</th>
                  <td><input type="number" name="pajak" class="form-control number-input" value="0"></td>
                </tr>
                <tr>
                    <th>Total Diskon</th>
                    <td><input type="number" name="total_diskon" class="form-control number-input" readonly></td>
                </tr>
                <tr>
                  <th>Biaya Kirim</th>
                  <td><input type="number" name="biaya_kirim" class="form-control number-input" value="0"></td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td><input type="number" name="total" class="form-control number-input" readonly></td>
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
  <td><input type="number" name="harga_beli[]" class="form-control harga number-input" required></td>
  <td><input type="number" name="diskon[]" class="form-control diskon number-input" min="0" value="0"></td>
  <td><input type="number" name="subtotal[]" class="form-control subtotal number-input" readonly></td>
  <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button></td>
</tr>
</template>

{{-- Select2 + kalkulasi --}}
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
      // pakai harga_dasar sebagai default harga beli
      row.find('.harga').val(data.harga_dasar ?? 0);
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

  function hitungTotal(){
    let subtotal = 0;
    let totalDiskon = 0;
    $('#produk-body tr').each(function(){
      const qty = parseFloat($(this).find('.qty').val()) || 0;
      const harga = parseFloat($(this).find('.harga').val()) || 0;
      const diskon = parseFloat($(this).find('.diskon').val()) || 0;
      const sub = (qty * harga) - diskon;
      $(this).find('.subtotal').val(formatRupiah(sub.toString(0)));
      subtotal += sub;
      totalDiskon += diskon;
    });
    const pajak = parseFloat($('[name="pajak"]').val()) || 0;
    const biaya = parseFloat($('[name="biaya_kirim"]').val()) || 0;
    const totalPajak = subtotal * pajak / 100;
    $('[name="total_subtotal"]').val(formatRupiah(subtotal.toString(0)));
    $('[name="total_diskon"]').val(totalDiskon.toFixed(0));
    $('[name="total"]').val((subtotal + totalPajak + biaya).toFixed(0));
  }

  $(document).on('input', '.qty,.harga,.diskon,[name="pajak"],[name="biaya_kirim"]', hitungTotal);
  $(function(){ initSelect2(); hitungTotal(); });
  
</script>
@endsection