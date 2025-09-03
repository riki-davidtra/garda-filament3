<?php

namespace App\Observers;

use Illuminate\Support\Facades\Storage;
use App\Models\JadwalDokumen;
use App\Models\User;
use App\Services\WhatsAppService;

class JadwalDokumenObserver
{
    public function creating(JadwalDokumen $jadwal)
    {
        if (!$jadwal->kode) {
            do {
                $kode = 'JD' . mt_rand(100000, 999999);
            } while (JadwalDokumen::where('kode', $kode)->exists());

            $jadwal->kode = $kode;
        }
    }

    public function created(JadwalDokumen $jadwal)
    {
        $this->sendWhatsAppNotification($jadwal, 'dibuat');
    }

    public function updated(JadwalDokumen $jadwal)
    {
        $importantColumns = ['jenis_dokumen_id', 'waktu_unggah_mulai', 'waktu_unggah_selesai'];
        $mainChanged = $jadwal->wasChanged($importantColumns);
        $toggleChanged = ((int) $jadwal->aktif) !== (int) $jadwal->getOriginal('aktif');
        if ($mainChanged || $toggleChanged) {
            $this->sendWhatsAppNotification($jadwal, 'diperbarui');
        }
    }

    protected function sendWhatsAppNotification(JadwalDokumen $jadwal, string $action)
    {
        // Pastikan jenis dokumen ada dan aktif
        if (!$jadwal->jenisDokumen) {
            return;
        }

        // Tentukan status
        $status = $jadwal->aktif ? 'Aktif' : 'Tidak Aktif';

        // Ambil user yang punya role terkait jenis dokumen dan nomor WhatsApp valid
        $users = User::whereHas('roles', fn($q) => $q->whereIn(
            'id',
            $jadwal->jenisDokumen->roles->pluck('id')
        ))->whereNotNull('nomor_whatsapp')->get();

        foreach ($users as $user) {
            WhatsAppService::sendMessage(
                $user->nomor_whatsapp,
                "Halo {$user->name}, jadwal telah {$action}:\n" .
                    "Kode: {$jadwal->kode}\n" .
                    "Jenis Dokumen: {$jadwal->jenisDokumen->nama}\n" .
                    "Status: {$status}\n" .
                    "Waktu Unggah: " . ($jadwal->waktu_unggah_mulai?->format('d-m-Y H:i') ?? '-') . " → " . ($jadwal->waktu_unggah_selesai?->format('d-m-Y H:i') ?? '-')
            );
        }
    }
}
