<?php

namespace App\Filament\Resources\DataDukungPerencanaanResource\Pages;

use App\Filament\Resources\DataDukungPerencanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDataDukungPerencanaan extends CreateRecord
{
    protected static string $resource = DataDukungPerencanaanResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     dd('dsada');
    //     $data['nama'] = $data['nama_select'] === 'other'
    //         ? ($data['nama_lainnya'] ?? null)
    //         : ($data['nama_select'] ?? null);

    //     // hapus field tambahan supaya tidak error insert
    //     unset($data['nama_select'], $data['nama_lainnya']);

    //     return $data;
    // }
}
