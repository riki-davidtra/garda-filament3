<?php

namespace App\Filament\Resources\TemplatDokumenResource\Pages;

use App\Filament\Resources\TemplatDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplatDokumen extends EditRecord
{
    protected static string $resource = TemplatDokumenResource::class;

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
