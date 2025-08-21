<?php

namespace App\Observers;

use App\Models\IndeksKinerjaUtama;
use Illuminate\Support\Facades\Auth;

class IndeksKinerjaUtamaObserver
{
    public function updating(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if ($indeksKinerjaUtama->isDirty()) {
            $indeksKinerjaUtama->revisi_ke = ($indeksKinerjaUtama->revisi_ke ?? 1) + 1;
        }
    }

    public function created(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'buat',
                'jenis_data'  => 'Indeks Kinerja Utama',
                'deskripsi'   => "User membuat formulir IKU: {$indeksKinerjaUtama->indikator->nama} - {$indeksKinerjaUtama->periode}",
                'detail_data' => json_encode($indeksKinerjaUtama->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => IndeksKinerjaUtama::class,
                'subjek_id'   => $indeksKinerjaUtama->id,
            ]);
        }
    }

    public function updated(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if (!app()->runningInConsole()) {
            $user    = Auth::user();
            $changes = [
                'before' => $indeksKinerjaUtama->getOriginal(),
                'after'  => $indeksKinerjaUtama->getDirty(),
            ];

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'ubah',
                'jenis_data'  => 'Indeks Kinerja Utama',
                'deskripsi'   => "User memperbarui formulir IKU: {$indeksKinerjaUtama->indikator->nama} - {$indeksKinerjaUtama->periode}",
                'detail_data' => json_encode($changes, JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => IndeksKinerjaUtama::class,
                'subjek_id'   => $indeksKinerjaUtama->id,
            ]);
        }
    }

    public function deleted(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            $aksi = $indeksKinerjaUtama->isForceDeleting() ? 'hapus permanen' : 'hapus';

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => $aksi,
                'jenis_data'  => 'Indeks Kinerja Utama',
                'deskripsi'   => "User menghapus permanen formulir IKU: {$indeksKinerjaUtama->indikator->nama} - {$indeksKinerjaUtama->periode}",
                'detail_data' => json_encode($indeksKinerjaUtama->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => IndeksKinerjaUtama::class,
                'subjek_id'   => $indeksKinerjaUtama->id,
            ]);
        }
    }

    public function restored(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'pulihkan',
                'jenis_data'  => 'Indeks Kinerja Utama',
                'deskripsi'   => "User memulihkan formulir IKU: {$indeksKinerjaUtama->indikator->nama} - {$indeksKinerjaUtama->periode}",
                'detail_data' => json_encode($indeksKinerjaUtama->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => IndeksKinerjaUtama::class,
                'subjek_id'   => $indeksKinerjaUtama->id,
            ]);
        }
    }
}
