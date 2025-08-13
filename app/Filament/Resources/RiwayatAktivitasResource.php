<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatAktivitasResource\Pages;
use App\Filament\Resources\RiwayatAktivitasResource\RelationManagers;
use App\Models\RiwayatAktivitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatAktivitasResource extends Resource
{
    protected static ?string $model = RiwayatAktivitas::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clock';
    protected static ?string $navigationLabel  = 'Riwayat Aktivitas';
    protected static ?string $pluralModelLabel = 'Daftar Riwayat Aktivitas';
    protected static ?string $modelLabel       = 'Riwayat Aktivitas';
    protected static ?int $navigationSort      = 15;

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
            'index'  => Pages\ListRiwayatAktivitas::route('/'),
            'create' => Pages\CreateRiwayatAktivitas::route('/create'),
            'edit'   => Pages\EditRiwayatAktivitas::route('/{record}/edit'),
        ];
    }
}
