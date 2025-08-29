<?php

namespace App\Filament\Resources\JadwalDokumenResource\Pages;

use App\Filament\Resources\JadwalDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalDokumens extends ListRecords
{
    protected static string $resource = JadwalDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
