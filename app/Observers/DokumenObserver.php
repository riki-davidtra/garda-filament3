<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;

class DokumenObserver
{
    public function deleting(Dokumen $dokumen): void
    {
        if ($dokumen->isForceDeleting()) {
            $dokumen->fileDokumens()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $dokumen->fileDokumens()->get()->each->delete();
        }
    }

    public function restoring(Dokumen $dokumen): void
    {
        $dokumen->fileDokumens()->onlyTrashed()->get()->each->restore();
    }

    public function created(Dokumen $dokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'buat',
                'deskripsi'   => "User membuat dokumen: {$dokumen->nama}",
                'ip'          => request()->ip(),
                'subjek_type' => Dokumen::class,
                'subjek_id'   => $dokumen->id,
            ]);
        }
    }

    public function updated(Dokumen $dokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'ubah',
                'deskripsi'   => "User mengubah dokumen: {$dokumen->nama}",
                'ip'          => request()->ip(),
                'subjek_type' => Dokumen::class,
                'subjek_id'   => $dokumen->id,
            ]);
        }
    }

    public function deleted(Dokumen $dokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            $aksi = $dokumen->isForceDeleting() ? 'hapus permanen' : 'hapus';

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => $aksi,
                'deskripsi'   => "User {$aksi} dokumen: {$dokumen->nama}",
                'ip'          => request()->ip(),
                'subjek_type' => Dokumen::class,
                'subjek_id'   => $dokumen->id,
            ]);
        }
    }

    public function restored(Dokumen $dokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'pulihkan',
                'deskripsi'   => "User memulihkan dokumen: {$dokumen->nama}",
                'ip'          => request()->ip(),
                'subjek_type' => Dokumen::class,
                'subjek_id'   => $dokumen->id,
            ]);
        }
    }
}
