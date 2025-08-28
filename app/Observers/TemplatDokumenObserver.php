<?php

namespace App\Observers;

use App\Models\TemplatDokumen;
use Illuminate\Support\Facades\Storage;

class TemplatDokumenObserver
{
    public function updating(TemplatDokumen $templatDokumen): void
    {
        if ($templatDokumen->isDirty('path')) {
            $originalValue = $templatDokumen->getOriginal('path');

            if ($originalValue && Storage::disk('public')->exists($originalValue)) {
                Storage::disk('public')->delete($originalValue);
            }
        }
    }

    public function deleting(TemplatDokumen $templatDokumen): void
    {
        if ($templatDokumen->path) {
            if (Storage::disk('public')->exists($templatDokumen->path)) {
                Storage::disk('public')->delete($templatDokumen->path);
            }
        }
    }
}
