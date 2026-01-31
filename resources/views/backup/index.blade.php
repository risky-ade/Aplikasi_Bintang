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
    <div class="card-header d-flex justify-content-between">
        <form method="POST" action="{{ route('backup.run') }}">
            @csrf
            <button class="btn btn-primary">
                <i class="fas fa-database"></i> Backup Sekarang
            </button>
        </form>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Ukuran</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $i => $file)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $file->getFilename() }}</td>
                    <td>{{ number_format($file->getSize() / 1024 / 1024, 2) }} MB</td>
                    <td>{{ date('d-m-Y H:i', $file->getMTime()) }}</td>
                    <td>
                        <a href="{{ route('backup.download', $file->getFilename()) }}"
                        class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i>
                        </a>
                        <form action="{{ route('backup.destroy', $file) }}"
                            method="POST"
                            class="form-delete d-inline">
                            @csrf
                            @method('POST')
                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada backup</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('form');

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
    });
});
</script>
@endsection