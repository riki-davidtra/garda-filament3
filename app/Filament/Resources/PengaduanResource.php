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
    protected static ?int $navigationSort      = 53;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', ['Menunggu'])->count();
    }
    protected static ?string $navigationBadgeTooltip = 'Jumlah pengaduan dengan status Menunggu.';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->disabledOn('edit'),
                Forms\Components\RichEditor::make('pesan')
                    ->label('Pesan')
                    ->required()
                    ->maxLength(3000)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('pengaduan/pesan')
                    ->columnSpanFull()
                    ->disabledOn('edit'),
                Forms\Components\RichEditor::make('tanggapan')
                    ->label('Tanggapan')
                    ->nullable()
                    ->maxLength(3000)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('pengaduan/tanggapan')
                    ->columnSpanFull()
                    ->hiddenOn('create'),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->inline()
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'proses'   => 'Proses',
                        'selesai'  => 'Selesai',
                    ])
                    ->default('Menunggu')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (!$user->hasRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('dibuat_oleh', $user->id);
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
                    ->limit(35)
                    ->formatStateUsing(fn($state) => strip_tags($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'proses'   => 'primary',
                        'selesai'  => 'success',
                        default    => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Dikirim Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dibuat_pada')
                    ->label('Dikirim Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Dibalas Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diperbarui_pada')
                    ->label('Dibalas Pada')
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
