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

            // Simpan file dengan nama unik bawaan Laravel
            $path = $file->store('data-dukung-perencanaan', 'local');

            // Enkripsi isi file & timpa ulang
            $contents = encrypt(file_get_contents(Storage::disk('local')->path($path)));
            Storage::disk('local')->put($path, $contents);

            // Metadata
            $nama      = $dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan';
            $versi     = ($dataDukungPerencanaan->count() ?? 0) + 1;
            $fileName  = $nama . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';

            // Simpan ke DB
            $dataDukungPerencanaan->path         = $path;
            $dataDukungPerencanaan->perubahan_ke = $versi;

            // Hapus temporary files
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

            $path = $file->store('data-dukung-perencanaan', 'local');

            $contents = encrypt(file_get_contents(Storage::disk('local')->path($path)));
            Storage::disk('local')->put($path, $contents);

            $nama     = $dataDukungPerencanaan->nama ?? 'data-dukung-perencanaan';
            $versi    = ($dataDukungPerencanaan->perubahan_ke ?? 0) + 1;
            $fileName = $nama . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';

            $dataDukungPerencanaan->path         = $path;
            $dataDukungPerencanaan->perubahan_ke = $versi;

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
