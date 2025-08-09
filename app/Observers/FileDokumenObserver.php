<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FileDokumen;

class FileDokumenObserver
{
    public function creating(FileDokumen $fileDokumen): void
    {
        if (empty($fileDokumen->user_id)) {
            $fileDokumen->user_id = Auth::user()->id;
        }

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
}
