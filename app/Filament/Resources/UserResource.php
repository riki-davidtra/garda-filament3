<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = 'Manajemen Pengguna';
    protected static ?string $navigationLabel  = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Daftar Pengguna';
    protected static ?string $modelLabel       = 'Pengguna';
    protected static ?int $navigationSort      = 11;

    public static function form(Form $form): Form
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->hasRole('Super Admin');

        return $form
            ->schema([
                Forms\Components\FileUpload::make('avatar_url')
                    ->label('Foto Profil')
                    ->nullable()
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->enableOpen()
                    ->enableDownload()
                    ->maxSize(2048),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->string()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->email()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->string()
                            ->minLength(6)
                            ->confirmed()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(fn($state) => !empty($state)),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->string()
                            ->minLength(6)
                            ->revealable()
                            ->dehydrated(fn($state) => !empty($state)),

                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->nullable()
                            ->numeric()
                            ->maxLength(18)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('nomor_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->nullable()
                            ->numeric()
                            ->maxLength(15),

                        Forms\Components\Select::make('subbagian_id')
                            ->label('Subbagian')
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->relationship(
                                'subbagian',
                                'nama',
                                fn($query) => $query->with('bagian')
                                    ->orderBy(
                                        \App\Models\Bagian::select('nama')->whereColumn('bagians.id', 'subbagians.bagian_id')
                                    )->orderBy('nama')
                            )
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->bagian->nama} - {$record->nama}"),

                        Forms\Components\Select::make('roles')
                            ->label('Peran')
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
                    ]),
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
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Foto Profil')
                    ->width(50)
                    ->height(50)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_whatsapp')
                    ->label('Nomor WhatsApp')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subbagian.nama')
                    ->label('Subbagian')
                    ->formatStateUsing(
                        fn($record) =>
                        "{$record->subbagian?->bagian?->nama} - {$record->subbagian?->nama}"
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
