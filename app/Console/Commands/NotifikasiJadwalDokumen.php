<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\JadwalDokumenJob;

class NotifikasiJadwalDokumen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifikasi-jadwal-dokumen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi jadwal dokumen    ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        JadwalDokumenJob::dispatchSync();
        $this->info('Notifikasi jadwal dokumen dikirim.');
    }
}
