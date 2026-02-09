<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;



class RunBackupJob implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        Cache::lock('backup-running', 3600)->block(5, function () {
            try {
                Log::channel('backup')->info('Backup job started');

                Artisan::call('backup:run');

                Log::channel('backup')->info('Backup job finished');
            } catch (\Throwable $e) {
                Log::channel('backup')->error('Backup failed', [
                    'message' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }
}
