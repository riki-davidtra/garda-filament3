<?php

namespace App\Filament\Resources\IndeksKinerjaUtamaResource\Pages;

use App\Filament\Resources\IndeksKinerjaUtamaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndeksKinerjaUtamas extends ListRecords
{
    protected static string $resource = IndeksKinerjaUtamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
