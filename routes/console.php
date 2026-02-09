<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Backup otomatis MINGGUAN (setiap Minggu jam 02:00)
Schedule::command('backup:run')
    ->weekly()
    ->sendOutputTo(storage_path('logs/backup.log'))
    ->sundays()
    ->at('02:00')
    // ->everyMinute()
    // ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(function () {
        logger()->error('Backup mingguan gagal');
    });

// Backup otomatis BULANAN (tanggal 1 jam 03:00)
Schedule::command('backup:run')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(function () {
        logger()->error('Backup bulanan gagal');
    });

Schedule::command('backup:clean')
    ->daily()
    ->at('01:00');
