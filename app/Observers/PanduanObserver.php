<?php

namespace App\Observers;

use Illuminate\Support\Facades\Storage;
use App\Models\Panduan;

class PanduanObserver
{
    public function updating(Panduan $panduan): void
    {
        if ($panduan->isDirty('file')) {
            $originalValue = $panduan->getOriginal('file');

            if ($originalValue && Storage::disk('public')->exists($originalValue)) {
                Storage::disk('public')->delete($originalValue);
            }
        }
    }

    public function deleting(Panduan $panduan): void
    {
        if ($panduan->file) {
            if (Storage::disk('public')->exists($panduan->file)) {
                Storage::disk('public')->delete($panduan->file);
            }
        }
    }
}
