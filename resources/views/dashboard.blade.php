@extends('layouts.main')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalPenjualan }}</h3>
                        <p>Penjualan</p>
                    </div>
                    <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    <a href="/sales/sales_invoices" class="small-box-footer">Selengkapnya <i
                    class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalProduk }}</h3>
                        <p>Produk</p>
                    </div>
                    <div class="icon"><i class="fas fa-box"></i></div>
                    <a href="/master_produk" class="small-box-footer">Selengkapnya <i
                    class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalPelanggan }}</h3>
                        <p>Pelanggan</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="/customers" class="small-box-footer">Selengkapnya <i
                    class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $totalPembelian }}</h3>
                        <p>Pembelian</p>
                    </div>
                    <div class="icon"><i class="fas fa-truck"></i></div>
                    <a href="/purchases/purchase_inv" class="small-box-footer">Selengkapnya <i
                    class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                </div>
                <!-- Main row -->
                <div class="row">
                    <section class="col-lg-6 connectedSortable"> 
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Grafik Penjualan & Pembelian</h3>
                                <form method="GET" >
                                <div class="row">
                                    <div class="col">
                                    <select name="year" class="form-control" onchange="this.form.submit()">
                                        @for($y = now()->year; $y >= now()->year - 1; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            Tahun {{ $y }}
                                        </option>
                                        @endfor
                                    </select>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <canvas id="salesChart" height="120"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="col-lg-5 connectedSortable">
                    <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Penjualan</span>
                            <span class="info-box-number">{{ rupiah($totalNominalPenjualan) }}</span>
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Penghasilan</span>
                            <span class="info-box-number">{{ rupiah($penghasilanBulanIni) }}</span>
                        </div>
                        </div>
                    </div>
                    </div>

                    </section>

                </div>
            </div>
        </section>
    </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const chartData = @json($months);

const penjualan = chartData.map(m => m.penjualan);
const pembelian = chartData.map(m => m.pembelian);

const ctx = document.getElementById('salesChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [
            {
                label: 'Penjualan',
                data: penjualan,
                tension: 0.4,
                fill: true
            },
            {
                label: 'Pembelian',
                data: pembelian,
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});
</script>
@endsection
