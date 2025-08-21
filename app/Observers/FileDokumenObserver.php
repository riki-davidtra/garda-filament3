<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FileDokumen;

class FileDokumenObserver
{
    public function creating(FileDokumen $fileDokumen): void
    {
        if ($fileDokumen->path && Storage::disk('local')->exists($fileDokumen->path)) {
            $fullPath = Storage::disk('local')->path($fileDokumen->path);

            $fileDokumen->nama   = basename($fileDokumen->path);
            $fileDokumen->tipe   = pathinfo($fileDokumen->nama, PATHINFO_EXTENSION);
            $fileDokumen->ukuran = filesize($fullPath);
        }
    }

    public function updating(FileDokumen $fileDokumen): void
    {
        if ($fileDokumen->isDirty('path')) {
            $originalPath = $fileDokumen->getOriginal('path');

            if ($originalPath && Storage::disk('local')->exists($originalPath)) {
                Storage::disk('local')->delete($originalPath);
            }

            if ($fileDokumen->path && Storage::disk('local')->exists($fileDokumen->path)) {
                $fullPath = Storage::disk('local')->path($fileDokumen->path);

                $fileDokumen->nama   = basename($fileDokumen->path);
                $fileDokumen->tipe   = pathinfo($fileDokumen->nama, PATHINFO_EXTENSION);
                $fileDokumen->ukuran = filesize($fullPath);
            }
        }
    }

    public function deleting(FileDokumen $fileDokumen): void
    {
        if ($fileDokumen->isForceDeleting()) {
            if ($fileDokumen->path && Storage::disk('local')->exists($fileDokumen->path)) {
                Storage::disk('local')->delete($fileDokumen->path);
            }
        }
    }

    public function created(FileDokumen $fileDokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'buat',
                'jenis_data'  => 'File Dokumen',
                'deskripsi'   => "User membuat data: {$fileDokumen->nama}",
                'detail_data' => json_encode($fileDokumen->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => FileDokumen::class,
                'subjek_id'   => $fileDokumen->id,
            ]);
        }
    }

    public function updated(FileDokumen $fileDokumen): void
    {
        if (!app()->runningInConsole()) {
            $user    = Auth::user();
            $changes = [
                'before' => $fileDokumen->getOriginal(),
                'after'  => $fileDokumen->getDirty(),
            ];

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'ubah',
                'jenis_data'  => 'File Dokumen',
                'deskripsi'   => "User memperbarui data: {$fileDokumen->nama}",
                'detail_data' => json_encode($changes, JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => FileDokumen::class,
                'subjek_id'   => $fileDokumen->id,
            ]);
        }
    }

    public function deleted(FileDokumen $fileDokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            $aksi = $fileDokumen->isForceDeleting() ? 'hapus permanen' : 'hapus';

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => $aksi,
                'jenis_data'  => 'File Dokumen',
                'deskripsi'   => "User meng{$aksi} data: {$fileDokumen->nama}",
                'detail_data' => json_encode($fileDokumen->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => FileDokumen::class,
                'subjek_id'   => $fileDokumen->id,
            ]);
        }
    }

    public function restored(FileDokumen $fileDokumen): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'pulihkan',
                'jenis_data'  => 'File Dokumen',
                'deskripsi'   => "User memulihkan data: {$fileDokumen->nama}",
                'detail_data' => json_encode($fileDokumen->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => FileDokumen::class,
                'subjek_id'   => $fileDokumen->id,
            ]);
        }
    }
}
