@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Edit Produk Hilang</h1>
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
      <form action="{{ route('product_losses.update', $productLoss->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $productLoss->tanggal->format('Y-m-d')) }}" required>
              </div>
              <div class="col-md-4">
                <label>Produk</label>
                <select name="master_produk_id" id="master_produk_id" class="form-control" required>
                  <option value="">-- Pilih Produk --</option>
                  @foreach($produk as $item)
                    <option value="{{ $item->id }}" {{ old('master_produk_id', $productLoss->master_produk_id) == $item->id ? 'selected' : '' }}>
                      {{ $item->nama_produk }} (Stok: {{ $item->stok }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label>Qty Hilang</label>
                <input type="number" name="qty" class="form-control" min="1" value="{{ old('qty', $productLoss->qty) }}" required>
              </div>
              <div class="col-md-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $productLoss->keterangan) }}">
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('product_losses.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<script>
$(function () {
  $('#master_produk_id').select2({
    placeholder: 'Pilih produk',
    allowClear: true,
    width: '100%'
  });
});
</script>
@endsection
