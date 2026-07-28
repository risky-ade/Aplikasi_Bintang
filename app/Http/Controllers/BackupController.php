<?php

namespace App\Http\Controllers;

use App\Jobs\RunBackupJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{

    public function index()
    {
        $path = storage_path('app/backup/Laravel');

        $files = File::exists($path)
            ? collect(File::files($path))->sortByDesc(fn ($f) => $f->getMTime())
            : collect();

        return view('backup.index', compact('files'));
    }

    public function run()
    {
        try {
            Artisan::call('backup:run');

            return redirect()
                ->route('backup.index')
                ->with('success', 'Backup database berhasil dibuat.');
                // ->with('success', Artisan::output());

        } catch (\Exception $e) {

            return redirect()
                ->route('backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
        // Artisan::call('backup:run');

        // return redirect()
        //     ->route('backup.index')
        //     ->with('success', 'Backup berhasil dijalankan.');
    }

    public function download($file)
    {
        $path = storage_path('app/backup/' . config('app.name') . '/'. $file);

        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan');
        }

        Log::channel('backup')->info('File backup didownload', [
            'file' => $file,
            'user_id' => Auth::id(),
        ]);

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
        Log::channel('backup')->info('File backup dihapus', [
            'file' => $file,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'File Backup berhasil dihapus.');
    }

    public function reload()
    {
        
        $path = storage_path('app/backup/Laravel');

        $files = File::exists($path)
            ? collect(File::files($path))->sortByDesc(fn ($f) => $f->getMTime())
            : collect();

        return view('backup.table', compact('files'));
    }

}
