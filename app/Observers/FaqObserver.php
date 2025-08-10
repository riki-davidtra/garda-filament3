<?php

namespace App\Observers;

use App\Models\Faq;

class FaqObserver
{
    public function __construct(
        protected \App\Services\FileContentCleanupService $cleanupService
    ) {}

    public function updating(Faq $faq): void
    {

        if ($faq->isDirty('jawaban')) {
            $oldContent = $faq->getOriginal('jawaban') ?? '';
            $newContent = $faq->jawaban ?? '';

            $oldFiles = $this->cleanupService->extractFilesFromContent($oldContent);
            $newFiles = $this->cleanupService->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->cleanupService->deleteFileByUrl($fileUrl);
            }
        }
    }

    public function deleting(Faq $faq): void
    {
        if (!empty($faq->jawaban)) {
            $this->cleanupService->deleteFilesFromContent($faq->jawaban);
        }
    }
}
