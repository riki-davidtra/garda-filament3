<?php

namespace App\Filament\Resources\DataDukungPerencanaanResource\Pages;

use App\Filament\Resources\DataDukungPerencanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;

class ViewDataDukungPerencanaan extends ViewRecord
{
    protected static string $resource = DataDukungPerencanaanResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            ListDataDukungPerencanaans::getUrl() => 'Daftar Data Dukung Perencanaan',
            'Detail',
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Data Dukung Perencanaan';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
        ];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->schema([
            Infolists\Components\Tabs::make('Tab')
                ->tabs([
                    Infolists\Components\Tabs\Tab::make('Utama')
                        ->schema([
                            Infolists\Components\TextEntry::make('nama')
                                ->label('Nama'),
                            Infolists\Components\TextEntry::make('keterangan')
                                ->label('Keterangan')
                                ->placeholder('-'),
                        ]),

                    Infolists\Components\Tabs\Tab::make('Riwayat Aktivitas')
                        ->schema([
                            Infolists\Components\TextEntry::make('pembuat.name')
                                ->label('Dibuat Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pembuat, $record->dibuat_pada)),

                            Infolists\Components\TextEntry::make('pembaru.name')
                                ->label('Diperbarui Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pembaru, $record->diperbarui_pada)),

                            Infolists\Components\TextEntry::make('penghapus.name')
                                ->label('Dihapus Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->penghapus, $record->dihapus_pada)),

                            Infolists\Components\TextEntry::make('pemulih.name')
                                ->label('Dipulihkan Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pemulih, $record->dipulihkan_pada)),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    protected function formatUserInfo($user, $tanggal): ?string
    {
        if (!$user && !$tanggal) return null;

        $bagian    = $user?->subbagian?->bagian?->nama;
        $subbagian = $user?->subbagian?->nama;

        $parts = [
            $user?->name,
            $user?->nip ? 'NIP: ' . $user->nip                            : null,
            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
        ];

        return implode(' | ', array_filter($parts));
    }
}
