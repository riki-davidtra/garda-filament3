<?php

namespace App\Observers;

use App\Models\TemplatDokumen;

class TemplatDokumenObserver
{
    public function deleting(TemplatDokumen $templatDokumen): void
    {
        $templatDokumen->fileTemplatDokumens->each(fn($file) => $file->delete());
    }
}
