<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;



class RunBackupJob implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        Artisan::call('backup:run');
    }
}
