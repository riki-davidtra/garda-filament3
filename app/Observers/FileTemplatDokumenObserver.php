<?php

namespace App\Observers;

use App\Models\FileTemplatDokumen;
use Illuminate\Support\Facades\Storage;

class FileTemplatDokumenObserver
{
    public function updating(FileTemplatDokumen $fileTemplatDokumen): void
    {
        if ($fileTemplatDokumen->isDirty('path')) {
            $originalValue = $fileTemplatDokumen->getOriginal('path');

            if ($originalValue && Storage::disk('public')->exists($originalValue)) {
                Storage::disk('public')->delete($originalValue);
            }
        }
    }

    public function deleting(FileTemplatDokumen $fileTemplatDokumen): void
    {
        if ($fileTemplatDokumen->path) {
            if (Storage::disk('public')->exists($fileTemplatDokumen->path)) {
                Storage::disk('public')->delete($fileTemplatDokumen->path);
            }
        }
    }
}
