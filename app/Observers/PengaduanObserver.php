<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Pengaduan;

class PengaduanObserver
{
    public function __construct(
        protected \App\Services\FileContentCleanupService $cleanupService
    ) {}

    public function updating(Pengaduan $pengaduan): void
    {
        if ($pengaduan->isDirty('pesan')) {
            $oldContent = $pengaduan->getOriginal('pesan') ?? '';
            $newContent = $pengaduan->pesan ?? '';

            $oldFiles = $this->cleanupService->extractFilesFromContent($oldContent);
            $newFiles = $this->cleanupService->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->cleanupService->deleteFileByUrl($fileUrl);
            }
        }

        if ($pengaduan->isDirty('tanggapan')) {
            $oldContent = $pengaduan->getOriginal('tanggapan') ?? '';
            $newContent = $pengaduan->tanggapan ?? '';

            $oldFiles = $this->cleanupService->extractFilesFromContent($oldContent);
            $newFiles = $this->cleanupService->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->cleanupService->deleteFileByUrl($fileUrl);
            }
        }
    }

    public function deleting(Pengaduan $pengaduan): void
    {
        if (!empty($pengaduan->pesan)) {
            $this->cleanupService->deleteFilesFromContent($pengaduan->pesan);
        }

        if (!empty($pengaduan->tanggapan)) {
            $this->cleanupService->deleteFilesFromContent($pengaduan->tanggapan);
        }
    }
}
