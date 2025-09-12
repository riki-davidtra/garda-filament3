<?php

namespace App\Observers;

use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileObserver
{
    public function creating(File $file): void
    {
        // Enkripsi file
        if (!empty($file->path)) {
            $path = $file->path;
            $tag  = $file->tag ?? null;

            // Simpan file dengan nama unik bawaan Laravel
            $directory  = str_replace('_', '-', strtolower($tag ?? 'file'));
            $storedPath = $path->store($directory, 'local');

            // Enkripsi isi file & timpa ulang
            $contents = encrypt(file_get_contents(Storage::disk('local')->path($storedPath)));
            Storage::disk('local')->put($storedPath, $contents);

            // Metadata 
            $nama        = $file->nama ?? basename($storedPath);
            $extension   = $path->getClientOriginalExtension();
            $lastVersion = File::where('model_type', $file->model_type)->where('model_id', $file->model_id)->where('tag', $file->tag)->max('versi');

            // Simpan ke DB
            $file->path   = $storedPath;
            $file->nama   = $nama;
            $file->tipe   = $extension ?? $path->getMimeType();
            $file->ukuran = $path->getSize();
            $file->tag    = $tag;
            $file->versi  = ($lastVersion ?? 0) + 1;

            // Hapus temporary file upload Livewire
            @unlink($path->getRealPath());
        }
    }

    public function updating(File $file): void
    {
        if (!empty($file->path) && $file->isDirty('path')) {
            $path = $file->path;
            $tag  = $file->tag ?? null;

            $directory  = str_replace(' ', '-', strtolower($tag ?? 'file'));
            $storedPath = $path->store($directory, 'local');

            $contents = encrypt(file_get_contents(Storage::disk('local')->path($storedPath)));
            Storage::disk('local')->put($storedPath, $contents);

            if ($file->getOriginal('path') && Storage::disk('local')->exists($file->getOriginal('path'))) {
                Storage::disk('local')->delete($file->getOriginal('path'));
            }

            $nama        = $file->nama ?? basename($storedPath);
            $extension   = $path->getClientOriginalExtension();
            $lastVersion = File::where('model_type', $file->model_type)->where('model_id', $file->model_id)->where('tag', $file->tag)->max('versi');

            $file->path   = $storedPath;
            $file->nama   = $nama;
            $file->tipe   = $extension ?? $path->getMimeType();
            $file->ukuran = $path->getSize();
            $file->tag    = $tag;
            $file->versi  = ($lastVersion ?? 0) + 1;

            @unlink($path->getRealPath());
        }

        // $this->incrementDataDukungPerencanaan($file);
    }

    public function deleting(File $file): void
    {
        if ($file->isForceDeleting()) {
            if ($file->path && Storage::disk('local')->exists($file->path)) {
                Storage::disk('local')->delete($file->path);
            }
        }
    }

    // protected function incrementDataDukungPerencanaan(File $file): void
    // {
    //     if ($file->model_type === 'App\Models\DataDukungPerencanaan') {
    //         $parent = $file->model;
    //         if ($parent && $parent->exists && empty($parent->perubahanIncremented)) {
    //             $parent->increment('perubahan_ke');
    //             $parent->perubahanIncremented = true;
    //         }
    //     }
    // }
}
