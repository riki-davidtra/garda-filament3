<?php

namespace App\Filament\Resources\TemplatDokumenResource\Pages;

use App\Filament\Resources\TemplatDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplatDokumens extends ListRecords
{
    protected static string $resource = TemplatDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
