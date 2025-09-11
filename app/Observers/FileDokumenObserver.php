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

            // Simpan file dengan nama unik bawaan Laravel
            $path = $file->store('file-dokumen', 'local');

            // Enkripsi isi file & timpa ulang
            $contents = encrypt(file_get_contents(Storage::disk('local')->path($path)));
            Storage::disk('local')->put($path, $contents);

            // Metadata
            $owner     = $fileDokumen->dokumen;
            $nama      = $owner?->nama ?? 'dokumen';
            $versi     = ($owner?->fileDokumens()->count() ?? 0) + 1;
            $fileName  = $nama . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension = $file->getClientOriginalExtension();

            // Simpan ke DB
            $fileDokumen->path   = $path;
            $fileDokumen->nama   = $fileName . '.' . $extension;
            $fileDokumen->tipe   = $extension ?? $file->getMimeType();
            $fileDokumen->ukuran = $file->getSize();

            // Hapus temporary file upload Livewire
            @unlink($file->getRealPath());
        }
    }

    public function updating(FileDokumen $fileDokumen): void
    {
        // Enkripsi file
        if (!empty($fileDokumen->path) && $fileDokumen->isDirty('path')) {
            $file = $fileDokumen->path;

            $path = $file->store('file-dokumen', 'local');

            $contents = encrypt(file_get_contents(Storage::disk('local')->path($path)));
            Storage::disk('local')->put($path, $contents);

            if ($fileDokumen->getOriginal('path') && Storage::disk('local')->exists($fileDokumen->getOriginal('path'))) {
                Storage::disk('local')->delete($fileDokumen->getOriginal('path'));
            }

            $owner     = $fileDokumen->dokumen;
            $nama      = $owner?->nama ?? 'dokumen';
            $versi     = ($owner?->fileDokumens()->count() ?? 0) + 1;
            $fileName  = $nama . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension = $file->getClientOriginalExtension();

            $fileDokumen->path   = $path;
            $fileDokumen->nama   = $fileName . '.' . $extension;
            $fileDokumen->tipe   = $extension ?? $file->getMimeType();
            $fileDokumen->ukuran = $file->getSize();

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
