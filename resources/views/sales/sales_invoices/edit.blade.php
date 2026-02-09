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
          <h1 class="m-0">Edit Faktur Penjualan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Edit Faktur Penjualan</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

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
    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    @endif

    <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <label>No Faktur</label>
              <input type="text" class="form-control" value="{{ $penjualan->no_faktur }}" readonly>
            </div>
            <div class="col-md-4">
              <label>Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::parse($penjualan->tanggal)->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
              <label>Pelanggan</label>
              <select name="pelanggan_id" id="pelanggan_id" class="form-control" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach ($pelanggan as $p)
                  <option value="{{ $p->id }}" {{ old('pelanggan_id',$penjualan->pelanggan_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label>No PO</label>
              <input type="text" name="no_po" class="form-control" value="{{ $penjualan->no_po }}">
            </div>
            <div class="col-md-6">
              <label>Status Pembayaran</label>
              <select name="status_pembayaran" class="form-control" required>
                <option value="Belum Lunas" {{ $penjualan->status_pembayaran == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="Lunas" {{ $penjualan->status_pembayaran == 'Lunas' ? 'selected' : '' }}>Lunas</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Biaya Pengiriman</label>
              <input type="hidden" name="biaya_kirim" class="biaya_kirim" value="{{ $penjualan->biaya_kirim }}">
              <input type="text" class="form-control biaya_kirim_display" value="{{ rupiah($penjualan->biaya_kirim) }}">
            </div>
            <div class="col-md-6">
              <label>Jatuh Tempo</label>
              <input type="date" name="jatuh_tempo" class="form-control" value="{{ old('jatuh_tempo', \Carbon\Carbon::parse($penjualan->jatuh_tempo)->format('Y-m-d')) }}" required>
            </div>
          </div>

          <div class="row">
            <div class="mb-4">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="produk-column">Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Diskon</th>
                    <th>Subtotal</th>
                    <th><button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+</button></th>
                  </tr>
                </thead>
                <tbody id="produk-body">
                  @foreach ($penjualan->detail as $detail)
                    <tr>
                      <td>
                        <select name="produk_id[]" class="form-control produk-select" required>
                          {{-- <select name="produk_id[]" class="form-control produk-select" {{ $isReturExists ? 'disabled' : '' }}> --}}
                          <option value="{{ $detail->master_produk_id }}" selected>{{ $detail->produk->nama_produk }}</option>
                        </select>
                      </td>
                      <td>
                        {{-- <input type="number" name="qty[]" value="{{ $detail->qty }}" class="form-control qty" required {{ $isReturExists ? 'readonly' : '' }}> --}}
                        <input type="number" name="qty[]" value="{{ $detail->qty }}" class="form-control number-input qty" required>
                      </td>
                      <td>
                        <input type="hidden" name="harga_jual[]" value="{{ $detail->harga_jual }}" class="harga" required {{ $isReturExists ? 'readonly' : '' }}>
                        <input type="text" value="{{ rupiah($detail->harga_jual) }}" class="form-control harga_display number-input " required {{ $isReturExists ? 'readonly' : '' }}>
                      </td>
                      <td>
                        <input type="hidden" name="diskon[]" value="{{ $detail->diskon }}" class="diskon">
                        <input type="text" value="{{ rupiah($detail->diskon) }}" class="form-control diskon_display number-input ">
                      </td>
                      <td>
                        <input type="hidden" name="subtotal[]" value="{{ $detail->subtotal }}" class="subtotal" readonly>
                        <input type="text" value="{{ rupiah($detail->subtotal) }}" class="form-control subtotal_display number-input" readonly>
                      </td>
                      <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">x</button></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="catatan"><strong>Catatan / Note</strong></label>
                <textarea name="catatan" class="form-control" rows="6">{{ $penjualan->catatan }}</textarea>
              </div>
            </div>

            <div class="col-md-6 align-self-end">
              <table class="table table-bordered">
                <tr>
                  <th>Subtotal</th>
                  <td>
                    <input type="hidden" name="total_subtotal" class="total_subtotal">
                    <input type="text" class="form-control total_subtotal_display number-input" readonly>
                  </td>
                </tr>
                <tr>
                  <th>PPN / Pajak (%)</th>
                  <td><input type="number" name="pajak" class="form-control number-input" value="{{ $penjualan->pajak }}"></td>
                </tr>
                <tr>
                  <th>Biaya Kirim</th>
                  <td>
                    <input type="hidden" name="biaya_kirim" class="biaya_kirim" value="{{ $penjualan->biaya_kirim }}">
                    <input type="text" class="form-control biaya_kirim_display number-input" value="{{ rupiah($penjualan->biaya_kirim) }}"readonly>
                  </td>
                </tr>
                {{-- <tr>
                  <th>Total Diskon</th>
                  <td>
                    <input type="hidden" name="total_diskon" class="total_diskon">
                    <input type="text" class="form-control total_diskon_display number-input " readonly>
                  </td>
                </tr> --}}
                <tr>
                  <th>Total Bayar</th>
                  <td>
                    <input type="hidden" name="total" class="total">
                    <input type="text"  class="form-control number-input total_display " readonly>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="text-right">
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <button type="submit" class="btn btn-sm btn-primary">Update Transaksi</button>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>

<template id="produk-row-template">
<tr>
  <td><select name="produk_id[]" class="form-control produk-select" required></select></td>
  <td><input type="number" name="qty[]" class="form-control number-input qty" required></td>
  <td>
    <input type="hidden" name="harga_jual[]" class="harga">
    <input type="text"class="form-control harga_display number-input" required>
  </td>
  <td>
    <input type="hidden" name="diskon[]" class="diskon">
    <input type="text" class="form-control diskon_display number-input" value="0">
  </td>
  <td>
    <input type="hidden" name="subtotal[]" class="subtotal">
    <input type="text" class="form-control subtotal_display number-input " readonly>
  </td>
  <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button></td>
</tr>
</template>

<script>
    function initSelect2() {
        $('.produk-select').select2({
            placeholder: 'Cari Produk...',
            ajax: {
                url: '{{ route("produk.search") }}',
                dataType: 'json',
                delay: 250,
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
            row.find('.harga_display').val('Rp ' + data.harga_jual.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
            row.find('.qty').val(1).trigger('input');
        });
    }

function tambahBaris() {
    const template = document.getElementById('produk-row-template').content.cloneNode(true);
    $('#produk-body').append(template);
    initSelect2(); // jalankan ulang select2 agar aktif 
}

    function hapusBaris(btn) {
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
    const totalPajak = (total * pajak) / 100;
    const grandTotal = total + totalPajak + biayaKirim;

    // set hidden
    $('[name="total_subtotal"]').val(total);
    $('[name="total_diskon"]').val(totalDiskon);
    $('[name="biaya_kirim"]').val(biayaKirim);
    $('[name="total"]').val(grandTotal);

    // set display
    $('.total_subtotal_display').val(formatRupiah(total));
    $('.total_diskon_display').val(formatRupiah(totalDiskon));
    $('.biaya_kirim_display').val(formatRupiah(biayaKirim));
    $('.total_display').val(formatRupiah(grandTotal));
}

$(document).ready(function () {
    initSelect2();
    hitungTotal();

    // trigger total setiap input berubah
    $(document).on('input', '.qty, .harga_display, .diskon_display, .biaya_kirim_display, [name="pajak"]', function () {
        // konversi display ke hidden murni dulu
        if ($(this).hasClass('harga_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).closest('tr').find('.harga').val(value);
        }
        if ($(this).hasClass('diskon_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).closest('tr').find('.diskon').val(value);
        }
        if ($(this).hasClass('biaya_kirim_display')) {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $('.biaya_kirim').val(value);
        }

        hitungTotal();
    });
});

</script>
<script>
$(function () {
    $('#pelanggan_id').select2({
        placeholder: 'Pilih Pelanggan',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection
