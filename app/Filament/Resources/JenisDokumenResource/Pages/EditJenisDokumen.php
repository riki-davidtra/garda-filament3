<?php

namespace App\Filament\Resources\JenisDokumenResource\Pages;

use App\Filament\Resources\JenisDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisDokumen extends EditRecord
{
    protected static string $resource = JenisDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
