<?php

namespace App\Filament\Resources\RiwayatAktivitasResource\Pages;

use App\Filament\Resources\RiwayatAktivitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatAktivitas extends EditRecord
{
    protected static string $resource = RiwayatAktivitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
