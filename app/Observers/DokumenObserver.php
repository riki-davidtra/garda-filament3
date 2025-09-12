<?php

namespace App\Observers;

use App\Models\Dokumen;
use App\Services\DokumenService;
use App\Services\WhatsAppService;

class DokumenObserver
{
    public function deleting(Dokumen $dokumen): void
    {
        if ($dokumen->isForceDeleting()) {
            $dokumen->files()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $dokumen->files()->get()->each->delete();
        }
    }

    public function restoring(Dokumen $dokumen): void
    {
        $dokumen->files()->onlyTrashed()->get()->each->restore();
    }

    public function created(Dokumen $dokumen): void
    {
        $dokumen->refresh();
        $this->kirimNotifikasi($dokumen);
    }

    public function updated(Dokumen $dokumen): void
    {
        $this->kirimNotifikasi($dokumen);
    }

    protected function kirimNotifikasi(Dokumen $dokumen): void
    {
        $notifikasi = DokumenService::notifikasiFind($dokumen);

        foreach ($notifikasi as $notif) {
            $user  = $notif['user'];
            $pesan = $notif['pesan'];
            WhatsAppService::sendMessage($user->nomor_whatsapp, $pesan);
        }
    }
}
