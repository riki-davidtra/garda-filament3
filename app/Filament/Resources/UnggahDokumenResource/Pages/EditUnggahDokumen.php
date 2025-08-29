<?php

namespace App\Filament\Resources\UnggahDokumenResource\Pages;

use App\Filament\Resources\UnggahDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnggahDokumen extends EditRecord
{
    protected static string $resource = UnggahDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
