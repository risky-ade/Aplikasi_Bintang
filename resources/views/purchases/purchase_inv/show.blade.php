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
          <h1 class="m-0">Detail Faktur Pembelian</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
            <li class="breadcrumb-item active">Detail Faktur</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
      <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <h5><strong>Informasi Pembelian</strong></h5>
        <div class="row">
          <div class="col-md-4">
            <p><strong>No Faktur:</strong> {{ $pembelian->no_faktur }}</p>
            <p><strong>Tanggal:</strong> {{ $pembelian->tanggal}}</p>
            <p><strong>Status:</strong> 
              <span class="badge {{ $pembelian->status_pembayaran == 'Lunas' ? 'badge-success' : 'badge-warning' }}">
                {{ $pembelian->status_pembayaran }}
              </span>
            </p>
          </div>
          <div class="col-md-4">
            <p><strong>No PO:</strong> {{ $pembelian->no_po ?? '-' }}</p>
            <p><strong>Catatan:</strong> {{ $pembelian->catatan ?? '-' }}</p>
          </div>
          <div class="col-md-4">
            <p><strong>Pemasok:</strong> {{ $pembelian->pemasok->nama }}</p>
            <p><strong>Alamat:</strong> {{ $pembelian->pemasok->alamat ?? '-' }}</p>
            {{-- <p><strong>Telepon:</strong> {{ $penjualan->pelanggan->no_hp ?? '-' }}</p> --}}
          </div>
        </div>

        <hr>

        <h5><strong>Daftar Produk</strong></h5>
        <div class="table-responsive">
          @php
              $grandNet = 0;
          @endphp
          <table class="table table-bordered table-striped">
            <thead class="text-white" style="background-color: #001f3f;">
              <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @php $total = 0; $total_diskon = 0; @endphp
              @foreach ($pembelian->detail as $i => $item)
               @php
                $pid = $item->master_produk_id; // atau $d->produk_id
                $retQty = ($produkDiretur[$pid] ?? 0);
                $qtyAwal = ($item ->qty ?? 0);
                $netQty = max(0, $item->qty - $retQty);
                $harga    = ($item->harga_beli ?? 0);
                $discTot   = ($item->diskon ?? 0);
                $discUnit = $qtyAwal > 0 ? $discTot / $qtyAwal : 0;

                $netSubtotal  = max(0, $netQty * $harga - $netQty * $discTot); 
                $grandNet += $netSubtotal;
              @endphp
              <tr class="{{ $netQty === 0 ? 'text-muted' : '' }}">
                <td>{{ $i+1 }}</td>
                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                <td class="text-right">{{ $netQty }}</td>
                <td class="text-right">{{ rupiah($item->harga_beli) }}</td>
                <td class="text-right">{{ rupiah($discTot) }}</td>
                <td class="text-right">{{ rupiah($netSubtotal) }}</td>
              </tr>
              {{-- @php
                $total += $item->subtotal;

                $pajakPersen = ($pembelian->pajak ?? 0); // misal 10 berarti 10%
                $biayaKirim  = ($pembelian->biaya_kirim ?? 0);

                $diskonNota = ($pembelian->diskon_nota ?? 0);
                $subtotDisc = max(0, $total - $diskonNota);
                $totalPajak  = ($subtotDisc * $pajakPersen) / 100;
                $grandTotal  = $subtotDisc + $totalPajak + $biayaKirim;
                // $diskon_nota+= $item->diskon_nota;
              @endphp --}}
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row justify-content-end">
          <div class="col-md-6">
             @php
              // $pajakPersen = ($pembelian->pajak ?? 0); 
              // $diskonNota  = ($pembelian->diskon_nota ?? 0);
              // $biayaKirim  = ($pembelian->biaya_kirim ?? 0);
              // $totalPajak  = ($grandNet * $pajakPersen) / 100;
              // $grandTotal  = $grandNet - $diskonNota + $totalPajak + $biayaKirim;

              $diskonNota = (float) ($pembelian->diskon_nota ?? 0);
              $subtotDisc = max(0, $grandNet - $diskonNota);

              $pajakPersen = ($pembelian->pajak ?? 0);
              $biayaKirim  = ($pembelian->biaya_kirim ?? 0);
              $totalPajak  = $subtotDisc * $pajakPersen / 100;
              $total       = $subtotDisc + $totalPajak + $biayaKirim;
            @endphp
            <table class="table table-sm table-borderless">
              <tr>
                <th class="text-right">Subtotal</th>
                <td class="text-right">{{ rupiah($grandNet) }}</td>
              </tr>
              <tr>
                <th class="text-right">Diskon Nota</th>
                <td class="text-right">{{ rupiah($diskonNota) }}</td>
              </tr>
              <tr>
                <th class="text-right">Pajak ({{ $pembelian->pajak }}%)</th>
                <td class="text-right">
                  {{ rupiah($totalPajak) }}
                </td>
              </tr>
              <tr>
                <th class="text-right">Biaya Kirim</th>
                <td class="text-right">{{ rupiah($biayaKirim) }}</td>
              </tr>
              <tr class="font-weight-bold">
                <th class="text-right">Total Bayar</th>
                <td class="text-right">{{ rupiah($total) }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="text-right mt-3">
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            {{-- <a href="{{ route('penjualan.print-pdf', $penjualan->id) }}" class="btn btn-sm btn-danger" target="_blank">
              <i class="fas fa-file-pdf"></i> PDF 
            </a> --}}
            <a href="{{ route('pembelian.print', $pembelian->id) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print"></i> Print Invoice</a>
          {{-- <a href="{{ route('penjualan.print-surat-jalan', $penjualan->id) }}" class="btn btn-info btn-sm" target="_blank">
              <i class="fas fa-print"></i> Print Surat Jalan
          </a>
          <a href="{{ route('penjualan.surat-jalan-pdf', $penjualan->id) }}" class="btn btn-danger btn-sm" target="_blank">
              <i class="fas fa-file-pdf"></i> PDF Surat Jalan
          </a> --}}
        </div>

      </div>
    </div>

  </div>
  </section>
@endsection