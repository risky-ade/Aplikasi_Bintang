@extends('layouts.main')
@section('content')
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
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Input Produk Hilang</h1>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <form action="{{ route('product_losses.store') }}" method="POST">
        @csrf
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
              </div>
            </div>

            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <thead class="bg-secondary text-white">
                  <tr>
                    <th style="min-width: 320px">Produk</th>
                    <th style="min-width: 140px">Qty Hilang</th>
                    <th style="min-width: 140px">Satuan</th>
                    <th style="min-width: 260px">Keterangan</th>
                    <th style="width: 70px">Aksi</th>
                  </tr>
                </thead>
                <tbody id="produk-body">
                  <tr>
                    <td>
                      <select name="master_produk_id[]" class="form-control produk-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $item)
                       <option
                          value="{{ $item->id }}"
                          data-stok="{{ $item->stok }}"
                          data-satuan="{{ $item->satuan->jenis_satuan }}">
                          {{ $item->nama_produk }} (Stok: {{ $item->stok }})
                      </option>
                        
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="qty[]" class="form-control qty-input" min="1" value="1" required></td>
                    <td><input type="text" name="satuan[]" class="form-control" readonly></td>
                    <td><input type="text" name="keterangan[]" class="form-control"></td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-success btn-sm" onclick="tambahBaris()">+ Tambah Produk</button>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('product_losses.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<template id="produk-row-template">
  <tr>
    <td>
      <select name="master_produk_id[]" class="form-control produk-select" required>
        <option value="">-- Pilih Produk --</option>
        @foreach($produk as $item)
          <option
              value="{{ $item->id }}"
              data-stok="{{ $item->stok }}"
              data-satuan="{{ $item->satuan->jenis_satuan }}">
              {{ $item->nama_produk }} (Stok: {{ $item->stok }})
          </option>
        @endforeach
      </select>
    </td>
    <td><input type="number" name="qty[]" class="form-control qty-input" min="1" value="1" required></td>
    <td><input type="text" name="satuan[]" class="form-control" readonly></td>
    <td><input type="text" name="keterangan[]" class="form-control"></td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button>
    </td>
  </tr>
</template>

<script>
  
  function initSelect2(context = document) {

    $(context).find('.produk-select').select2({
        placeholder: 'Pilih produk',
        allowClear: true,
        width: '100%'
    }).on('select2:select', function (e) {

        let data = e.params.data;

        let option = $(data.element);

        let row = $(this).closest('tr');

        row.find('input[name="satuan[]"]').val(option.attr('data-satuan'));

    });

}

function tambahBaris() {
  const template = document.getElementById('produk-row-template').content.cloneNode(true);
  $('#produk-body').append(template);
  initSelect2($('#produk-body tr:last'));
}

function hapusBaris(btn) {
  if ($('#produk-body tr').length > 1) {
    $(btn).closest('tr').remove();
  }
}

$(function () {
  initSelect2();
});
</script>
@endsection
