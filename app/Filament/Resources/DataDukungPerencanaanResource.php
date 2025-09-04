<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataDukungPerencanaanResource\Pages;
use App\Filament\Resources\DataDukungPerencanaanResource\RelationManagers;
use App\Models\DataDukungPerencanaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Get;
use Filament\Forms\Set;

class DataDukungPerencanaanResource extends Resource
{
    protected static ?string $model = DataDukungPerencanaan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Formulir';
    protected static ?string $navigationLabel  = 'Data Dukung Perencanaan';
    protected static ?string $pluralModelLabel = 'Daftar Data Dukung Perencanaan';
    protected static ?string $modelLabel       = 'Data Dukung Perencanaan';
    protected static ?int $navigationSort      = 42;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nama')
                    ->label('Nama Data Dukung')
                    ->required()
                    ->options([
                        'Aset'                               => 'Aset',
                        'Kepegawaian'                        => 'Kepegawaian',
                        'Laporan Pengadaaan Barang dan Jasa' => 'Laporan Pengadaaan Barang dan Jasa',
                        'lainnya'                            => 'Lainnya',
                    ])
                    ->live()
                    ->afterStateHydrated(function ($state, Set $set) {
                        if ($state && !in_array($state, [
                            'Aset',
                            'Kepegawaian',
                            'Laporan Pengadaaan Barang dan Jasa'
                        ])) {
                            $set('nama', 'lainnya');
                            $set('nama_input', $state);
                        }
                    }),

                Forms\Components\TextInput::make('nama_input')
                    ->label('Nama Lainnya')
                    ->required()
                    ->maxLength(255)
                    ->visible(fn(Get $get): bool => $get('nama') === 'lainnya')
                    ->dehydrateStateUsing(
                        fn($state, Get $get) =>
                        $get('nama') === 'lainnya' ? $state : null
                    )
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($get('nama') === 'lainnya') {
                            $set('nama', $state);
                        }
                    }),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->maxLength(3000)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('path')
                    ->label('File Dokumen')
                    ->nullable()
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

    public static function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');
        $isSubbagian    = $user->hasRole('subbagian');

        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire)  use ($user, $isSuperOrAdmin, $isPerencana) {
                if (!$isSuperOrAdmin && !$isPerencana) {
                    $query->where('dibuat_oleh', $user->id);
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Data Dukung')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('perubahan_ke')
                    ->label('Perubahan ke')
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Diisi Oleh')
                    ->description(function ($record) {
                        $user      = $record->pembuat;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dibuat_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Direvisi Oleh')
                    ->description(function ($record) {
                        $user      = $record->pembaru;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->diperbarui_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penghapus.name')
                    ->label('Dihapus Oleh')
                    ->description(function ($record) {
                        $user      = $record->penghapus;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dihapus_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pemulih.name')
                    ->label('Dipulihkan Oleh')
                    ->description(function ($record) {
                        $user      = $record->pemulih;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dipulihkan_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => $isSuperOrAdmin),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('file-data-dukung-perencanaan.unduh', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record?->path) && Storage::disk('local')->exists($record->path)),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataDukungPerencanaans::route('/'),
            // 'create' => Pages\CreateDataDukungPerencanaan::route('/create'),
            // 'edit'   => Pages\EditDataDukungPerencanaan::route('/{record}/edit'),
        ];
    }
}
