@extends('layouts.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Item Master Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Tambah Item</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
   
        <section class="content">
        <div class="container">
            <form action="{{ url('/master_produk') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('master_produk.form', ['master_produk' => null])
                <div class="card-footer text-right">
                    <button type="submit"  id="btnSubmit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('/master_produk') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
        </section>
      
    </div>
    <script>
$(document).ready(function () {

    $('#nama_produk').on('keyup change', function () {
        let nama = $(this).val();

        if (nama.length < 2) {
            // Reset jika kosong
            $(this).removeClass('is-invalid');
            $('#namaError').text('');
            $('#btnSubmit').prop('disabled', false);
            return;
        }

        $.ajax({
            url: "{{ route('produk.check-duplicate') }}",
            type: "GET",
            data: { nama_produk: nama },
            success: function (res) {
                if (res.exists) {
                    $('#nama_produk').addClass('is-invalid');
                    $('#namaError').text('Nama produk sudah dipakai!');
                    $('#btnSubmit').prop('disabled', true);
                } else {
                    $('#nama_produk').removeClass('is-invalid');
                    $('#namaError').text('');
                    $('#btnSubmit').prop('disabled', false);
                }
            }
        });

    });

});
</script>
@endsection
