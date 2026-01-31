<?php

namespace App\Http\Controllers;

use App\Jobs\RunBackupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{

    public function index()
    {
        // $files = collect(Storage::files('LaravelApp'))
        //     ->sortDesc();
        $path = storage_path('app/backup/Laravel');

        $files = File::exists($path)
            ? collect(File::files($path))->sortByDesc(fn ($f) => $f->getMTime())
            : collect();

        return view('backup.index', compact('files'));
    }

    public function run()
    {
        // Artisan::call('backup:run');

        // return redirect()
        //     ->route('backup.index')
        //     ->with('success', 'Backup berhasil dijalankan.');
        RunBackupJob::dispatch();

        return back()->with('success', 'Backup sedang diproses di background.');
    }

    public function download($file)
    {
        // return Storage::download('LaravelApp/'.$file);
        $path = storage_path('app/backup/' . config('app.name') . '/'. $file);

        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan');
        }

        return response()->download($path);
    }

    public function destroy($file)
    {
       $file = basename(urldecode($file));

        $path = storage_path('app/backup/Laravel/' . $file);

        if (!file_exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        unlink($path);

        return back()->with('success', 'File Backup berhasil dihapus.');
    }

}
