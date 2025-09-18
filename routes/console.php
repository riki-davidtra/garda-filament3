<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\JadwalDokumenJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// app(Schedule::class)->command('notifikasi-jadwal-dokumen')->dailyAt('00:00');
// app(Schedule::class)->job(new JadwalDokumenJob)->everyMinute();
app(Schedule::class)->job(new JadwalDokumenJob)->dailyAt('08:00');
