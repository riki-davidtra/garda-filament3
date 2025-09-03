<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\JadwalDokumen;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\JadwalDokumenService;

class JadwalDokumenJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notifikasi = JadwalDokumenService::notifikasiAll();
        foreach ($notifikasi as $notif) {
            $user         = $notif['user'];
            $pesanLengkap = $notif['pesan'];
            WhatsAppService::sendMessage($user->nomor_whatsapp, $pesanLengkap);
        }
    }
}
