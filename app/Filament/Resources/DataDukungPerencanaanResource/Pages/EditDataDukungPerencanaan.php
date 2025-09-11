<?php

namespace App\Filament\Resources\DataDukungPerencanaanResource\Pages;

use App\Filament\Resources\DataDukungPerencanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataDukungPerencanaan extends EditRecord
{
    protected static string $resource = DataDukungPerencanaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\ViewAction::make()
                ->label('Detail')
                ->icon('heroicon-o-eye'),
        ];
    }
}
