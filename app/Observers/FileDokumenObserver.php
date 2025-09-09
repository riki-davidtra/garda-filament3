<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FileDokumen;

class FileDokumenObserver
{
    public function creating(FileDokumen $fileDokumen): void
    {
        // Enkripsi file
        if (!empty($fileDokumen->path)) {
            $file = $fileDokumen->path;

            $owner       = $fileDokumen->dokumen;
            $namaDokumen = $owner?->nama ?? 'dokumen';
            $versi       = ($owner?->fileDokumens()->count() ?? 0) + 1;
            $fileName    = $namaDokumen . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-dokumen/{$fileName}.{$extension}";

            Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            $fileDokumen->path   = $path;
            $fileDokumen->nama   = $fileName . '.' . $extension;
            $fileDokumen->tipe   = $extension ?? $file->getMimeType();
            $fileDokumen->ukuran = $file->getSize();

            @unlink($file->getRealPath());
        }
    }

    public function updating(FileDokumen $fileDokumen): void
    {
        // Enkripsi file
        if (!empty($fileDokumen->path) && $fileDokumen->isDirty('path')) {
            $file = $fileDokumen->path;

            $owner       = $fileDokumen->dokumen;
            $namaDokumen = $owner?->nama ?? 'dokumen';
            $versi       = ($owner?->fileDokumens()->count() ?? 0) + 1;
            $fileName    = $namaDokumen . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-dokumen/{$fileName}.{$extension}";

            // Enkripsi file baru
            Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            // Hapus file lama kalau ada
            if ($fileDokumen->getOriginal('path') && Storage::disk('local')->exists($fileDokumen->getOriginal('path'))) {
                Storage::disk('local')->delete($fileDokumen->getOriginal('path'));
            }

            // Simpan path baru ke DB             
            $fileDokumen->path   = $path;
            $fileDokumen->nama   = $fileName . '.' . $extension;
            $fileDokumen->tipe   = $extension ?? $file->getMimeType();
            $fileDokumen->ukuran = $file->getSize();

            // Hapus temporary file upload Livewire
            @unlink($file->getRealPath());
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
}
