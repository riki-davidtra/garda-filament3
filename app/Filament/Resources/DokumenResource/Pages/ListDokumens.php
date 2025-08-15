<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDokumens extends ListRecords
{
    protected static string $resource = DokumenResource::class;

    public ?int $jenis_dokumen_id = null;

    public function mount(): void
    {
        parent::mount();

        // Mengambil parameter jenis_dokumen_id dari query string
        $this->jenis_dokumen_id = request()->query('jenis_dokumen_id');
    }

    public function getBreadcrumbs(): array
    {
        // Custom navigasi breadcrumbs untuk halaman ini
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Daftar',
        ];
    }

    protected function getHeaderActions(): array
    {
        // Memberi parameter jenis_dokumen_id pada URL
        return [
            Actions\CreateAction::make()
                ->url(fn() => DokumenResource::getUrl('create', [
                    'jenis_dokumen_id' => $this->jenis_dokumen_id,
                ])),

        ];
    }

    protected function getQueryString(): array
    {
        return [
            // Menyimpan jenis_dokumen_id di query string kecuali jika null & menyertakan query string default dari parent
            'jenis_dokumen_id' => [
                'except' => null,
            ],
            ...parent::getQueryString(),
        ];
    }
}
