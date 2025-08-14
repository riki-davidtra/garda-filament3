<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisDokumenResource\Pages;
use App\Filament\Resources\JenisDokumenResource\RelationManagers;
use App\Models\JenisDokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisDokumenResource extends Resource
{
    protected static ?string $model = JenisDokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Data Master';
    protected static ?string $navigationLabel  = 'Jenis Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Jenis Dokumen';
    protected static ?string $modelLabel       = 'Jenis Dokumen';
    protected static ?int $navigationSort      = 23;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('waktu_unggah_mulai')
                    ->label('Waktu Unggah Mulai')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('waktu_unggah_selesai')
                    ->label('Waktu Unggah Selesai')
                    ->nullable(),
                Forms\Components\TextInput::make('batas_unggah')
                    ->label('Batas Unggah')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_unggah_mulai')
                    ->label('Waktu Unggah')
                    ->formatStateUsing(function ($record) {
                        $mulai = $record->waktu_unggah_mulai?->format('Y-m-d H:i');
                        $selesai = $record->waktu_unggah_selesai?->format('Y-m-d H:i');
                        return "{$mulai} → {$selesai}";
                    })
                    ->color(fn($record) => match (true) {
                        $record->waktu_unggah_mulai && now()->lt($record->waktu_unggah_mulai) => 'gray',
                        $record->waktu_unggah_selesai && now()->gt($record->waktu_unggah_selesai) => 'danger',
                        default => 'success',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batas_unggah')
                    ->label('Batas Unggah')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListJenisDokumens::route('/'),
            // 'create' => Pages\CreateJenisDokumen::route('/create'),
            // 'edit' => Pages\EditJenisDokumen::route('/{record}/edit'),
        ];
    }
}
