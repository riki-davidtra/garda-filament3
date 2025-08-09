<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDokumen extends EditRecord
{
    protected static string $resource = DokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        if (! auth()->user()->hasRole(['Super Admin', 'admin', 'perencana'])) {
            return [];
        }

        return parent::getFormActions();
    }
}
