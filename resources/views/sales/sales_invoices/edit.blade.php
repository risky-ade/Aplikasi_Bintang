@extends('layouts.main')
@section('content')

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
              <input type="date" name="tanggal" class="form-control" value="{{ $penjualan->tanggal }}" required>
            </div>
            <div class="col-md-4">
              <label>Pelanggan</label>
              <select name="pelanggan_id" class="form-control" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach ($pelanggan as $p)
                  <option value="{{ $p->id }}" {{ $penjualan->pelanggan_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
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
              <input type="number" name="biaya_kirim" class="form-control" value="{{ $penjualan->biaya_kirim }}">
            </div>
            <div class="col-md-6">
              <label>Jatuh Tempo</label>
              <input type="date" name="jatuh_tempo" class="form-control" value="{{ $penjualan->jatuh_tempo }}" required>
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
                      <td><input type="number" name="harga_jual[]" value="{{ $detail->harga_jual }}" class="form-control number-input harga" required {{ $isReturExists ? 'readonly' : '' }}></td>
                      <td><input type="number" name="diskon[]" value="{{ $detail->diskon }}" class="form-control number-input diskon"></td>
                      <td><input type="number" name="subtotal[]" value="{{ $detail->subtotal }}" class="form-control number-input subtotal" readonly></td>
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
                  <td><input type="number" name="total_subtotal" class="form-control number-input" readonly></td>
                </tr>
                <tr>
                  <th>PPN / Pajak (%)</th>
                  <td><input type="number" name="pajak" class="form-control number-input" value="{{ $penjualan->pajak }}"></td>
                </tr>
                <tr>
                  <th>Biaya Kirim</th>
                  <td><input type="number" name="biaya_kirim" class="form-control number-input" value="{{ $penjualan->biaya_kirim }}"></td>
                </tr>
                <tr>
                  <th>Total Diskon</th>
                  <td><input type="number" name="total_diskon" class="form-control number-input" readonly></td>
                </tr>
                <tr>
                  <th>Total Bayar</th>
                  <td><input type="number" name="total" class="form-control total number-input" readonly></td>
                </tr>
              </table>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Update Transaksi</button>
        </div>
      </div>
    </form>
  </section>
</div>

<template id="produk-row-template">
<tr>
  <td><select name="produk_id[]" class="form-control produk-select" required></select></td>
  <td><input type="number" name="qty[]" class="form-control number-input qty" required></td>
  <td><input type="number" name="harga_jual[]" class="form-control number-input harga" required></td>
  <td><input type="number" name="diskon[]" class="form-control number-input diskon" value="0"></td>
  <td><input type="number" name="subtotal[]" class="form-control number-input subtotal" readonly></td>
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

    function hitungTotal() {
        let total = 0;
        let totalDiskon = 0;

        $('#produk-body tr').each(function () {
            const qty = parseFloat($(this).find('.qty').val()) || 0;
            const harga = parseFloat($(this).find('.harga').val()) || 0;
            const diskon = parseFloat($(this).find('.diskon').val()) || 0;
            const subtotal = (qty * harga) - diskon;
            $(this).find('.subtotal').val(subtotal.toFixed(0));

            total += subtotal;
            totalDiskon += diskon;
        });

        const pajak = parseFloat($('[name="pajak"]').val()) || 0;
        const biaya_kirim = parseFloat($('[name="biaya_kirim"]').val()) || 0;
        const totalPajak = (total * pajak) / 100;

        $('[name="total_subtotal"]').val(total.toFixed(0));
        $('[name="total_diskon"]').val(totalDiskon.toFixed(0));
        $('[name="biaya_kirim"]').val(biaya_kirim.toFixed(0));
        $('[name="total"]').val((total + totalPajak + biaya_kirim).toFixed(0));
    }

    $(document).ready(function () {
    initSelect2();

    // Optional: trigger kalkulasi pertama kali
    hitungTotal();

    // Pastikan select2 aktif ulang setelah tambah baris
    $(document).on('input', '.qty, .harga, .diskon, [name="pajak"], [name="biaya_kirim"]', function () {
        hitungTotal();
    });
});

</script>
@endsection
