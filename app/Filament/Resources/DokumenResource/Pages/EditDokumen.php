<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDokumen extends EditRecord
{
    protected static string $resource = DokumenResource::class;

    public ?int $jenis_dokumen_id = null;

    public function mount(string|int $record): void
    {
        parent::mount($record);

        // Mengambil parameter jenis_dokumen_id dari query string
        $this->jenis_dokumen_id = request()->query('jenis_dokumen_id');
    }

    public function getBreadcrumbs(): array
    {
        // Custom navigasi breadcrumbs untuk halaman ini
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Ubah',
        ];
    }

    protected function getHeaderActions(): array
    {
        // Memberi parameter jenis_dokumen_id pada URL
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    $this->redirect(ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]));
                })
        ];
    }
}
