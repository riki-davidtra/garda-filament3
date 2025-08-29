<?php

namespace App\Observers;

use Illuminate\Support\Facades\Storage;
use App\Models\JadwalDokumen;

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
}
