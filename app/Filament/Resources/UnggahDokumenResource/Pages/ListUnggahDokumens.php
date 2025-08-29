<?php

namespace App\Filament\Resources\UnggahDokumenResource\Pages;

use App\Filament\Resources\UnggahDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnggahDokumens extends ListRecords
{
    protected static string $resource = UnggahDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
