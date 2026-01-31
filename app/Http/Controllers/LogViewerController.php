<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        $file = $request->get('file', 'laravel.log');
        $path = storage_path('logs/' . $file);

        if (!File::exists($path)) {
            abort(404, 'Log file tidak ditemukan');
        }

        $logs = collect(file($path))
            ->reverse()
            ->take(500);

        return view('logs.index', compact('logs', 'file'));
    }
}
