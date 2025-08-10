<?php

namespace App\Observers;

use Illuminate\Support\Facades\Storage;
use App\Models\Panduan;

class PanduanObserver
{
    public function __construct(
        protected \App\Services\FileContentCleanupService $cleanupService
    ) {}

    public function updating(Panduan $panduan): void
    {
        if ($panduan->isDirty('file')) {
            $originalValue = $panduan->getOriginal('file');

            if ($originalValue && Storage::disk('public')->exists($originalValue)) {
                Storage::disk('public')->delete($originalValue);
            }
        }

        if ($panduan->isDirty('deskripsi')) {
            $oldContent = $panduan->getOriginal('deskripsi') ?? '';
            $newContent = $panduan->deskripsi ?? '';

            $oldFiles = $this->cleanupService->extractFilesFromContent($oldContent);
            $newFiles = $this->cleanupService->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->cleanupService->deleteFileByUrl($fileUrl);
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

        if (!empty($panduan->deskripsi)) {
            $this->cleanupService->deleteFilesFromContent($panduan->deskripsi);
        }
    }
}
