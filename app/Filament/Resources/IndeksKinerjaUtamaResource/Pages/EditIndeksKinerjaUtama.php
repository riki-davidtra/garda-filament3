<?php

namespace App\Filament\Resources\IndeksKinerjaUtamaResource\Pages;

use App\Filament\Resources\IndeksKinerjaUtamaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndeksKinerjaUtama extends EditRecord
{
    protected static string $resource = IndeksKinerjaUtamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
