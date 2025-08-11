<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaduanResource\Pages;
use App\Filament\Resources\PengaduanResource\RelationManagers;
use App\Models\Pengaduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PengaduanResource extends Resource
{
    protected static ?string $model = Pengaduan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup  = 'Panduan & Bantuan';
    protected static ?string $navigationLabel  = 'Pengaduan';
    protected static ?string $pluralModelLabel = 'Daftar Pengaduan';
    protected static ?string $modelLabel       = 'Pengaduan';
    protected static ?int $navigationSort      = 33;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', ['menunggu'])->count();
    }
    protected static ?string $navigationBadgeTooltip = 'Jumlah pengaduan dengan status menunggu.';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->string()
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->disabledOn('edit'),
                Forms\Components\RichEditor::make('pesan')
                    ->label('Pesan')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(3000)
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'link',
                    ])
                    ->disabledOn('edit'),
                Forms\Components\RichEditor::make('tanggapan')
                    ->label('Tanggapan')
                    ->nullable()
                    ->columnSpanFull()
                    ->maxLength(3000)
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'link',
                    ])
                    ->hiddenOn('create'),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->inline()
                    ->options([
                        'menunggu' => 'Menunggu',
                        'proses'   => 'Proses',
                        'selesai'  => 'Selesai',
                    ])
                    ->default('menunggu')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (!$user->hasRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('user_id', $user->id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pesan')
                    ->label('Pesan')
                    ->limit(30)
                    ->formatStateUsing(fn($state) => strip_tags($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'proses'   => 'primary',
                        'selesai'  => 'success',
                        default    => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPengaduans::route('/'),
            // 'create' => Pages\CreatePengaduan::route('/create'),
            // 'edit' => Pages\EditPengaduan::route('/{record}/edit'),
        ];
    }
}
