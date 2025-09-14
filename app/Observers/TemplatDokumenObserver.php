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
        if ($templatDokumen->isForceDeleting()) {
            $templatDokumen->files()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $templatDokumen->files()->get()->each->delete();
        }
    }

    public function restoring(TemplatDokumen $templatDokumen): void
    {
        $templatDokumen->files()->onlyTrashed()->get()->each->restore();
    }
}
