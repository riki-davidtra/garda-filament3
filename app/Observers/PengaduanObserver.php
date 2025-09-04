<?php

namespace App\Observers;

use App\Models\Pengaduan;

class PengaduanObserver
{

    public function __construct(
        protected \App\Services\TiptapEditorFileCleanupService $tiptapEditorCleanup,
    ) {}

    public function updating(Pengaduan $pengaduan): void
    {
        if ($pengaduan->isDirty('pesan')) {
            $oldContent = $pengaduan->getOriginal('pesan') ?? '';
            $newContent = $pengaduan->pesan ?? '';

            $oldFiles = $this->tiptapEditorCleanup->extractFilesFromContent($oldContent);
            $newFiles = $this->tiptapEditorCleanup->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->tiptapEditorCleanup->deleteFileByUrl($fileUrl);
            }
        }

        if ($pengaduan->isDirty('tanggapan')) {
            $oldContent = $pengaduan->getOriginal('tanggapan') ?? '';
            $newContent = $pengaduan->tanggapan ?? '';

            $oldFiles = $this->tiptapEditorCleanup->extractFilesFromContent($oldContent);
            $newFiles = $this->tiptapEditorCleanup->extractFilesFromContent($newContent);

            $deletedFiles = array_diff($oldFiles, $newFiles);

            foreach ($deletedFiles as $fileUrl) {
                $this->tiptapEditorCleanup->deleteFileByUrl($fileUrl);
            }
        }
    }

    public function deleting(Pengaduan $pengaduan): void
    {
        if (!empty($pengaduan->pesan)) {
            $this->tiptapEditorCleanup->deleteFilesFromContent($pengaduan->pesan);
        }

        if (!empty($pengaduan->tanggapan)) {
            $this->tiptapEditorCleanup->deleteFilesFromContent($pengaduan->tanggapan);
        }
    }
}
