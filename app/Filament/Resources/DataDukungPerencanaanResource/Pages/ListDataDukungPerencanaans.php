<?php

namespace App\Filament\Resources\DataDukungPerencanaanResource\Pages;

use App\Filament\Resources\DataDukungPerencanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataDukungPerencanaans extends ListRecords
{
    protected static string $resource = DataDukungPerencanaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
