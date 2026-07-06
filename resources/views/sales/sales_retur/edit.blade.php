@extends('layouts.main')
@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h3 class="mb-3">Edit Retur Penjualan</h3>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <form method="POST" action="{{ route('retur-penjualan.update', $retur->id) }}">
        @csrf
        @method('PUT')
        <div class="card">
          <div class="card-body">
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-3">
              <div class="col-md-4">
                <label>No Retur</label>
                <input type="text" class="form-control" value="{{ $retur->no_retur }}" readonly>
              </div>
              <div class="col-md-4">
                <label>Faktur Penjualan</label>
                <input type="text" class="form-control" value="{{ $retur->penjualan->no_faktur ?? '-' }} - {{ $retur->penjualan->pelanggan->nama ?? '-' }}" readonly>
              </div>
              <div class="col-md-4">
                <label for="tanggal_retur">Tanggal Retur</label>
                <input type="date" name="tanggal_retur" class="form-control" required value="{{ old('tanggal_retur', $retur->getRawOriginal('tanggal_retur')) }}">
              </div>
            </div>

            <div class="mb-3">
              <label>Alasan Retur</label>
              <textarea name="alasan" class="form-control" rows="2" placeholder="Opsional...">{{ old('alasan', $retur->alasan) }}</textarea>
            </div>

            <h5 class="mb-3">Detail Produk Penjualan</h5>
            <table class="table table-bordered">
              <thead class="bg-secondary text-white">
                <tr>
                  <th>Produk</th>
                  <th>Qty Jual</th>
                  <th>Sisa Bisa Retur</th>
                  <th>Harga Jual</th>
                  <th>Diskon</th>
                  <th>Qty Retur</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($penjualanDetails as $index => $detail)
                  @php
                    $produkId = $detail->master_produk_id;
                    $existing = $detailRetur[$produkId] ?? null;
                    $qtyJual = (int) $detail->qty;
                    $qtyReturLain = (int) ($returLainnya[$produkId] ?? 0);
                    $maxRetur = max(0, $qtyJual - $qtyReturLain);
                    $qtyValue = old('qty_retur.' . $index, $existing->qty_retur ?? 0);
                    $diskonTotal = (float) ($detail->diskon ?? 0);
                    $diskonUnit = $diskonTotal / max(1, $qtyJual);
                    $subtotal = max(0, (int) $qtyValue * ((float) $detail->harga_jual - $diskonUnit));
                  @endphp
                  <tr>
                    <td>
                      {{ $detail->produk->nama_produk ?? '-' }}
                      <input type="hidden" name="produk_id[]" value="{{ $produkId }}">
                    </td>
                    <td>{{ $qtyJual }}</td>
                    <td>{{ $maxRetur }}</td>
                    <td>Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($diskonTotal, 0, ',', '.') }}</td>
                    <td>
                      <input type="number" name="qty_retur[]" class="form-control qty-retur" min="0" max="{{ $maxRetur }}" value="{{ $qtyValue }}" data-harga="{{ $detail->harga_jual }}" data-discunit="{{ $diskonUnit }}">
                    </td>
                    <td class="subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <button type="submit" class="btn btn-primary mt-3">Update Retur</button>
            <a href="{{ route('retur-penjualan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<script>
$(document).on('input', '.qty-retur', function () {
  const harga = Number($(this).data('harga') || 0);
  const discUnit = Number($(this).data('discunit') || 0);
  const qty = Number($(this).val() || 0);
  const netUnit = Math.max(0, harga - discUnit);
  const sub = Math.max(0, qty * netUnit);
  $(this).closest('tr').find('.subtotal').text('Rp ' + Math.round(sub).toLocaleString('id-ID'));
});
</script>
@endsection