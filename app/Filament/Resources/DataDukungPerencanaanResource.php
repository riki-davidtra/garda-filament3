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

class DataDukungPerencanaanResource extends Resource
{
    protected static ?string $model = DataDukungPerencanaan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Formulir';
    protected static ?string $navigationLabel  = 'Data Dukung';
    protected static ?string $pluralModelLabel = 'Daftar Data Dukung Perencanaan';
    protected static ?string $modelLabel       = 'Data Dukung';
    protected static ?int $navigationSort      = 42;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index'  => Pages\ListDataDukungPerencanaans::route('/'),
            'create' => Pages\CreateDataDukungPerencanaan::route('/create'),
            'edit'   => Pages\EditDataDukungPerencanaan::route('/{record}/edit'),
        ];
    }
}
