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
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['nama'] = $data['nama_select'] === 'other'
            ? ($data['nama_lainnya'] ?? null)
            : ($data['nama_select'] ?? null);

        unset($data['nama_select'], $data['nama_lainnya']);

        return $data;
    }
}
