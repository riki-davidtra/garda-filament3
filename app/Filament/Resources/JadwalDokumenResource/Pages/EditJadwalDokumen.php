<?php

namespace App\Filament\Resources\JadwalDokumenResource\Pages;

use App\Filament\Resources\JadwalDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalDokumen extends EditRecord
{
    protected static string $resource = JadwalDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
