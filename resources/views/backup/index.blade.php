@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Backup Database</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Backup Database</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
<section class="content">
<div class="container-fluid">

<div class="card">
    <div class="card-header d-flex ">
        <form method="POST" action="{{ route('backup.run') }}">
            @csrf
            <button class="btn btn-primary">
                <i class="fas fa-database"></i> Backup Sekarang
            </button>
        </form>
        <button id="reload-table" class="btn btn-primary mb-3 mx-3">
            <i class="fas fa-sync"></i> Reload Tabel
        </button>
    </div>

    <div id="table-container">
        @include('backup.table')
    </div>
</div>

</div>
</section>
</div>

<script>
$(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();

    let form = $(this).closest('form');

    Swal.fire({
        title: 'Yakin hapus backup?',
        text: 'File backup akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
<script>
$('#reload-table').click(function(){
    $("#table-container").load(location.href + " #table-container>*", "");
});
</script>
@endsection