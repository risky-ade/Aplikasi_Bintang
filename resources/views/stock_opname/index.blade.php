@extends('layouts.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Stok Opname</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Stok Opname</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('stock_opname.create') }}" class="btn btn-primary mb-2"><i class="fas fa-solid fa-plus"></i>Tambah</a>
                  </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover w-100 nowrap" id="DataTable">
                  <thead class="bg-secondary text-white">
                    <tr>
                        <th>No</th>
                        <th>No Opname</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                        <th>Approve</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->no_opname }}</td>
                        <td>{{ $d->tanggal }}</td>
                        <td>{{ $d->user->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $d->status == 'draft' ? 'warning' : 'success' }}">
                                {{ $d->status }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('stock_opname.show', $d->id) }}" class="btn btn-info btn-sm">
                               <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('stock_opname.edit', $d->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if($d->status == 'draft')
                            <form action="{{ route('stock_opname.destroy', $d->id) }}" method="POST" class="d-inline form-delete-opname">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                        <td>
                            @if($d->status == 'draft')
                            <form method="POST" action="{{ route('stock_opname.approve',$d->id) }}">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
              </div>
              </div>
              </div>
            </div>
        </div>
      </div>
    </section>

       
    </div>
<script>
    $(document).ready(function() {
    $('#DataTable').DataTable({
        autoWidth: false,    
        responsive: false,    
        pageLength: 10,
        lengthMenu: [10, 15, 25, 50, 100],
        columnDefs: [
        { targets: [0,1,2,4,5,6], className: 'text-nowrap' },
        { targets: [3], width: '220px' }
        ],
        language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ baris per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari total _MAX_ data)",
        paginate: { next: "Berikutnya", previous: "Sebelumnya" }
        },
    });
    });
</script>
<script>
    $(document).on('submit', '.form-delete-opname', function(e) {
        e.preventDefault();

        const form = this;

        Swal.fire({
            title: 'Hapus stock opname?',
            text: 'Data draft yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection
