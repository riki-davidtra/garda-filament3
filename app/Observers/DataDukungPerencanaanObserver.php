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
            $file = $dataDukungPerencanaan->path;

            $namaDokumen = $dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan';
            $versi       = 1;
            $fileName    = $namaDokumen . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-data-dukung-perencanaan/{$fileName}.{$extension}";

            // Enkripsi file 
            Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            // Simpan hanya path ke DB
            $dataDukungPerencanaan->path = $path;

            // Hapus temporary filessssss
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

            $namaDokumen = $dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan';
            $versi       = $dataDukungPerencanaan->perubahan_ke;
            $fileName    = $namaDokumen . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-data-dukung-perencanaan/{$fileName}.{$extension}";

            // Enkripsi file baru
            Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            // Hapus file lama kalau ada
            if ($dataDukungPerencanaan->getOriginal('path') && Storage::disk('local')->exists($dataDukungPerencanaan->getOriginal('path'))) {
                Storage::disk('local')->delete($dataDukungPerencanaan->getOriginal('path'));
            }

            // Simpan path baru ke DB
            $dataDukungPerencanaan->path = $path;

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
}
