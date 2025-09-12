<?php

namespace App\Filament\Resources\DataDukungPerencanaanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';
    protected static ?string $title       = 'Daftar File Data Dukung Perencanaan';
    protected static ?string $label       = 'File Data Dukung Perencanaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('tag')->default('data_dukung_perencanaan'),

                Forms\Components\FileUpload::make('path')
                    ->label('File')
                    ->required()
                    ->storeFiles(false)
                    ->disk('local')
                    ->directory('temp')
                    ->maxSize(20480)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->state(function ($record) {
                        return sprintf(
                            '%s (v%s).%s',
                            $record->model?->nama,
                            $record->versi ?? 1,
                            $record->tipe
                        );
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->formatStateUsing(fn($state) => number_format($state / 1024, 2) . ' KB')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('versi')
                    ->label('Versi')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => $isSuperOrAdmin),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Unggah')
                    ->modalHeading('Unggah File Data Dukung Perencanaan'),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('file-data-dukung-perencanaan.unduh', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record?->path) && Storage::disk('local')->exists($record->path)),

                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail File Data Dukung Perencanaan')
                    ->button()
                    ->infolist(function ($record) {
                        $formatUserInfo = function ($user, $tanggal) {
                            $bagian    = $user?->subbagian?->bagian?->nama;
                            $subbagian = $user?->subbagian?->nama;
                            $parts     = [
                                $user?->name,
                                $user?->nip ? 'NIP: ' . $user->nip                            : null,
                                $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                                $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                            ];
                            return implode(' | ', array_filter($parts));
                        };

                        return [
                            Infolists\Components\Tabs::make('Tab')
                                ->tabs([
                                    Infolists\Components\Tabs\Tab::make('Utama')
                                        ->schema([
                                            Infolists\Components\TextEntry::make('nama')->label('Nama')
                                                ->state(function ($record) {
                                                    return sprintf(
                                                        '%s (v%s).%s',
                                                        $record->model?->nama,
                                                        $record->versi ?? 1,
                                                        $record->tipe
                                                    );
                                                }),

                                            Infolists\Components\TextEntry::make('tipe')->label('Tipe')
                                                ->state($record->tipe),

                                            Infolists\Components\TextEntry::make('ukuran')
                                                ->label('Ukuran')
                                                ->state(number_format(($record->ukuran ?? 0) / 1024, 2) . ' KB'),
                                        ]),

                                    Infolists\Components\Tabs\Tab::make('Riwayat Aktivitas')
                                        ->schema([
                                            Infolists\Components\TextEntry::make('pembuat.name')
                                                ->label('Dibuat Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembuat, $record->dibuat_pada)),

                                            Infolists\Components\TextEntry::make('pembaru.name')
                                                ->label('Diperbarui Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembaru, $record->diperbarui_pada)),

                                            Infolists\Components\TextEntry::make('penghapus.name')
                                                ->label('Dihapus Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->penghapus, $record->dihapus_pada)),

                                            Infolists\Components\TextEntry::make('pemulih.name')
                                                ->label('Dipulihkan Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pemulih, $record->dipulihkan_pada)),
                                        ])
                                        ->columns(2),
                                ]),
                        ];
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
