<?php

namespace App\Filament\Resources\JadwalDokumenResource\Pages;

use App\Filament\Resources\JadwalDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalDokumens extends ListRecords
{
    protected static string $resource = JadwalDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('informasi')
                ->label('Informasi')
                ->button()
                ->color('info')
                ->icon('heroicon-o-information-circle')
                ->modalHeading('Informasi Jadwal Dokumen')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalWidth('md')
                ->modalContent(fn() => view('filament.components.informasi-jadwal-dokumen')),

            Actions\CreateAction::make(),
        ];
    }
}
