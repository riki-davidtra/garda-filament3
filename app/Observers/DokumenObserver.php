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

    public function forceDeleted(Dokumen $dokumen): void
    {
        foreach ($dokumen->files()->withTrashed()->get() as $file) {
            $file->forceDelete();
        }
    }
}
