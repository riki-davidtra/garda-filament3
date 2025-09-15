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
use Illuminate\Support\Facades\Auth;

class JenisDokumenResource extends Resource
{
    protected static ?string $model = JenisDokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Data Master';
    protected static ?string $navigationLabel  = 'Jenis Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Jenis Dokumen';
    protected static ?string $modelLabel       = 'Jenis Dokumen';
    protected static ?int $navigationSort      = 24;

    public static function form(Form $form): Form
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->string()
                    ->maxLength(255),

                Forms\Components\TextInput::make('batas_unggah')
                    ->label('Batas Unggah')
                    ->nullable()
                    ->numeric()
                    ->default(0),

                Forms\Components\Select::make('format_file')
                    ->label('Format File')
                    ->nullable()
                    ->multiple()
                    ->options(\App\Models\FormatFile::all()->pluck('nama', 'id'))
                    ->dehydrateStateUsing(fn($state) => array_map('intval', $state)),

                Forms\Components\TextInput::make('maksimal_ukuran')
                    ->label('Maks. Ukuran')
                    ->nullable()
                    ->numeric()
                    ->suffix('KB')
                    ->default(0),

                Forms\Components\Fieldset::make('Mode Penggunaan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('mode_status')
                            ->label('Mode Status')
                            ->nullable()
                            ->default(false),

                        Forms\Components\Toggle::make('mode_subkegiatan')
                            ->label('Mode Subkegiatan')
                            ->nullable()
                            ->default(false),

                        Forms\Components\Toggle::make('mode_periode')
                            ->label('Mode Periode')
                            ->nullable()
                            ->default(false),
                    ]),

                Forms\Components\Select::make('roles')
                    ->label('Akses Peran')
                    ->nullable()
                    ->multiple()
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query)  use ($isSuperAdmin) {
                            if (!$isSuperAdmin) {
                                $query->where('name', '!=', 'Super Admin');
                            }
                        }
                    )
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire) use ($isSuperAdmin) {
                if (!$isSuperAdmin) {
                    $query->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'Super Admin');
                    });
                }

                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('batas_unggah')
                    ->label('Batas Unggah')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('mode_status')
                    ->label('Mode Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Akses Peran')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
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
            'index' => Pages\ListJenisDokumens::route('/'),
            // 'create' => Pages\CreateJenisDokumen::route('/create'),
            // 'edit' => Pages\EditJenisDokumen::route('/{record}/edit'),
        ];
    }
}
