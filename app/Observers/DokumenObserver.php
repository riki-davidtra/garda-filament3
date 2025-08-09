<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;

class DokumenObserver
{
    public function creating(Dokumen $dokumen): void
    {
        if (empty($dokumen->user_id)) {
            $dokumen->user_id = Auth::user()->id;
        }
    }

    public function deleting(Dokumen $dokumen): void
    {
        if ($dokumen->isForceDeleting()) {
            $dokumen->fileDokumens()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        }
    }
}
