<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumenResource\Pages;
use App\Filament\Resources\DokumenResource\RelationManagers;
use App\Models\Dokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DokumenResource extends Resource
{
    protected static ?string $model = Dokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Manajemen Dokumen';
    protected static ?string $navigationLabel  = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Dokumen';
    protected static ?string $modelLabel       = 'Dokumen';
    protected static ?int $navigationSort      = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->maxLength(4)
                    ->numeric(),
                Forms\Components\Select::make('subbagian_id')
                    ->label('Subbagian')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('subbagian', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    }),
                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    }),
                Forms\Components\DateTimePicker::make('tenggat_waktu')
                    ->label('Tenggat Waktu')
                    ->nullable(),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->inline()
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('menunggu'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->columnSpanFull()
                    ->maxLength(3000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subbagian.nama')
                    ->label('Subbagian')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisDokumen.nama')
                    ->label('Jenis Dokumen')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenggat_waktu')
                    ->label('Tenggat Waktu')
                    ->dateTime()
                    ->sortable()
                    ->color(
                        fn($record) =>
                        now()->lte($record->tenggat_waktu)
                            ? 'success'
                            : 'danger'
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'primary'  => 'aktif',
                        'success'  => 'terpenuhi',
                        'danger'   => 'terlambat',
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
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
            RelationManagers\FileDokumensRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDokumens::route('/'),
            'create' => Pages\CreateDokumen::route('/create'),
            'edit'   => Pages\EditDokumen::route('/{record}/edit'),
        ];
    }
}
