<?php

namespace App\Filament\Resources\JenisDokumenResource\Pages;

use App\Filament\Resources\JenisDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisDokumens extends ListRecords
{
    protected static string $resource = JenisDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
