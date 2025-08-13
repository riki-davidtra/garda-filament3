<?php

namespace App\Filament\Resources\RiwayatAktivitasResource\Pages;

use App\Filament\Resources\RiwayatAktivitasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatAktivitas extends ListRecords
{
    protected static string $resource = RiwayatAktivitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
