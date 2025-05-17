@extends('components.main')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">List Produk</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">List Produk</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">

    {{-- @if(session()->has('success'))
    <div class="alert alert-success col-lg-8" role="alert">
      {{ session('success') }}
    </div>
  @endif --}}
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
           <div class="card">
              <div class="card-header">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <a href="/add_items">
                  <button class="btn btn-primary me-md-2" type="button"><i class="fas fa-solid fa-plus"></i>Tambah Data</button>
                  </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Item Id</th>
                    <th>Nama</th>
                    <th>Qyt</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Trident</td>
                    <td>Internet
                      Explorer 4.0
                    </td>
                    <td>Win 95+</td>
                    <td> 4</td>
                    <td>X</td>
                    <td>Edit/Hapus</td>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                  <tr>
                    <td>001</td>
                    <td>Kertas HVS 70</td>
                    <td>20</td>
                    <td>Rim</td>
                    <td>42.000</td>
                    <td>Edit/Hapus</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Item Id</th>
                    <th>Nama</th>
                    <th>Qyt</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Aksi</th>
                  </tr>
                </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
      </div>
    </div>
  </div>       
  </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2024-2025 <a href="#">CV.Bintang Empat</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@endsection