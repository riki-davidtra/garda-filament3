<?php

namespace App\Observers;

use App\Models\DataDukungPerencanaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DataDukungPerencanaanObserver
{
    public function creating(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        // Enkripsi file
        if (!empty($dataDukungPerencanaan->path)) {
            $file      = $dataDukungPerencanaan->path;
            $safeName  = \Illuminate\Support\Str::slug($dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan') . '-' . mt_rand(1000, 9999);
            $extension = $file->getClientOriginalExtension();
            $path      = "file-data-dukung-perencanaan/{$safeName}.{$extension}";

            // Enkripsi file
            $contents = file_get_contents($file->getRealPath());
            Storage::disk('local')->put($path, encrypt($contents));

            // Simpan hanya path ke DB
            $dataDukungPerencanaan->path = $path;

            // Hapus temporary file
            @unlink($file->getRealPath());
        }
    }

    public function updating(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if ($dataDukungPerencanaan->isDirty()) {
            $dataDukungPerencanaan->perubahan_ke = ($dataDukungPerencanaan->perubahan_ke ?? 1) + 1;
        }

        // Enkripsi file
        if (!empty($dataDukungPerencanaan->path) && $dataDukungPerencanaan->isDirty('path')) {
            $file = $dataDukungPerencanaan->path;

            $safeName  = \Illuminate\Support\Str::slug($dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan') . '-' . mt_rand(100000, 999999);
            $extension = $file->getClientOriginalExtension();
            $newPath   = "file-data-dukung-perencanaan/{$safeName}.{$extension}";

            // Enkripsi file baru
            $contents = file_get_contents($file->getRealPath());
            Storage::disk('local')->put($newPath, encrypt($contents));

            // Hapus file lama kalau ada
            if ($dataDukungPerencanaan->getOriginal('path') && Storage::disk('local')->exists($dataDukungPerencanaan->getOriginal('path'))) {
                Storage::disk('local')->delete($dataDukungPerencanaan->getOriginal('path'));
            }

            // Simpan path baru ke DB
            $dataDukungPerencanaan->path = $newPath;

            // Hapus temporary file upload Livewire
            @unlink($file->getRealPath());
        }
    }

    public function deleting(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if ($dataDukungPerencanaan->isForceDeleting()) {
            if ($dataDukungPerencanaan->path && Storage::disk('local')->exists($dataDukungPerencanaan->path)) {
                Storage::disk('local')->delete($dataDukungPerencanaan->path);
            }
        }
    }

    public function created(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'buat',
                'jenis_data'  => 'Data Dukung Perencanaan',
                'deskripsi'   => "User membuat data: {$dataDukungPerencanaan->nama}",
                'detail_data' => json_encode($dataDukungPerencanaan->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => DataDukungPerencanaan::class,
                'subjek_id'   => $dataDukungPerencanaan->id,
            ]);
        }
    }

    public function updated(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if (!app()->runningInConsole()) {
            $user    = Auth::user();
            $changes = [
                'before' => $dataDukungPerencanaan->getOriginal(),
                'after'  => $dataDukungPerencanaan->getDirty(),
            ];

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'ubah',
                'jenis_data'  => 'Data Dukung Perencanaan',
                'deskripsi'   => "User memperbarui data: {$dataDukungPerencanaan->nama}",
                'detail_data' => json_encode($changes, JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => DataDukungPerencanaan::class,
                'subjek_id'   => $dataDukungPerencanaan->id,
            ]);
        }
    }

    public function deleted(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            $aksi = $dataDukungPerencanaan->isForceDeleting() ? 'hapus permanen' : 'hapus';

            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => $aksi,
                'jenis_data'  => 'Data Dukung Perencanaan',
                'deskripsi'   => "User meng{$aksi} data: {$dataDukungPerencanaan->nama}",
                'detail_data' => json_encode($dataDukungPerencanaan->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => DataDukungPerencanaan::class,
                'subjek_id'   => $dataDukungPerencanaan->id,
            ]);
        }
    }

    public function restored(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if (!app()->runningInConsole()) {
            $user = Auth::user();
            \App\Models\RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => 'pulihkan',
                'jenis_data'  => 'Data Dukung Perencanaan',
                'deskripsi'   => "User memulihkan data: {$dataDukungPerencanaan->nama}",
                'detail_data' => json_encode($dataDukungPerencanaan->getAttributes(), JSON_PRETTY_PRINT),
                'ip'          => request()->ip(),
                'subjek_type' => DataDukungPerencanaan::class,
                'subjek_id'   => $dataDukungPerencanaan->id,
            ]);
        }
    }
}
