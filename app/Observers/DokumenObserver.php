<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;

class DokumenObserver
{
    public function deleting(Dokumen $dokumen): void
    {
        if ($dokumen->isForceDeleting()) {
            $dokumen->fileDokumens()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $dokumen->fileDokumens()->get()->each->delete();
        }
    }

    public function restoring(Dokumen $dokumen): void
    {
        $dokumen->fileDokumens()->onlyTrashed()->get()->each->restore();
    }
}
