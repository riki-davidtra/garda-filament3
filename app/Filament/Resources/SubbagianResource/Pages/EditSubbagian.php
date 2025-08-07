<?php

namespace App\Filament\Resources\SubbagianResource\Pages;

use App\Filament\Resources\SubbagianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubbagian extends EditRecord
{
    protected static string $resource = SubbagianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
