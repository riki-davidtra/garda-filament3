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

    protected static ?string $navigationIcon   = 'heroicon-o-tag';
    protected static ?string $navigationGroup  = 'Manajemen Dokumen';
    protected static ?string $navigationLabel  = 'Jenis Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Jenis Dokumen';
    protected static ?string $modelLabel       = 'Jenis Dokumen';
    protected static ?int $navigationSort      = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->string()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
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
