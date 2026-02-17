<div class="card-body" id="backup-table">
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
                    <th scope="file">{{ $loop->iteration }}</th>
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