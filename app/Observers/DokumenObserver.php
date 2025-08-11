<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;

class DokumenObserver
{
    public function __construct(
        protected \App\Services\FileContentCleanupService $cleanupService
    ) {}

    public function creating(Dokumen $dokumen): void
    {
        if (empty($dokumen->user_id)) {
            $dokumen->user_id = Auth::user()->id;
        }
    }

    public function updating(Dokumen $dokumen): void
    {

        if ($dokumen->isDirty('keterangan')) {
            $oldContent = $dokumen->getOriginal('keterangan') ?? '';
            $newContent = $dokumen->keterangan ?? '';

            $oldFiles = $this->cleanupService->extractFilesFromContent($oldContent);
            $newFiles = $this->cleanupService->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->cleanupService->deleteFileByUrl($fileUrl);
            }
        }
    }

    public function deleting(Dokumen $dokumen): void
    {
        if ($dokumen->isForceDeleting()) {
            $dokumen->fileDokumens()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $dokumen->fileDokumens()->get()->each->delete();
        }

        if ($dokumen->isForceDeleting()) {
            if (!empty($dokumen->keterangan)) {
                $this->cleanupService->deleteFilesFromContent($dokumen->keterangan);
            }
        }
    }

    public function restoring(Dokumen $dokumen): void
    {
        $dokumen->fileDokumens()->onlyTrashed()->get()->each->restore();
    }
}
