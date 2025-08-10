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
use Illuminate\Support\Facades\Auth;

class DokumenResource extends Resource
{
    protected static ?string $model = Dokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Manajemen Dokumen';
    protected static ?string $navigationLabel  = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Dokumen';
    protected static ?string $modelLabel       = 'Dokumen';
    protected static ?int $navigationSort      = 21;

    public static function form(Form $form): Form
    {
        $isReadOnly = !auth()->user()->hasRole(['Super Admin', 'admin', 'perencana']);

        return $form
            ->schema([
                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->maxLength(4)
                    ->numeric()
                    ->disabled($isReadOnly),
                Forms\Components\Select::make('subbagian_id')
                    ->label('Subbagian')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('subbagian', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->disabled($isReadOnly),
                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->disabled($isReadOnly),
                Forms\Components\DateTimePicker::make('tenggat_waktu')
                    ->label('Tenggat Waktu')
                    ->nullable()
                    ->disabled($isReadOnly),
                Forms\Components\RichEditor::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->columnSpanFull()
                    ->maxLength(3000)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('dokumen/keterangan')
                    ->disabled($isReadOnly),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->inline()
                    ->options([
                        'menunggu'  => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ])
                    ->default('menunggu')
                    ->disabled($isReadOnly),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (!$user->hasRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('subbagian_id', $user->subbagian_id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subbagian.nama')
                    ->label('Subbagian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenisDokumen.nama')
                    ->label('Jenis Dokumen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_dokumens_count')
                    ->label('Jumlah File')
                    ->counts('fileDokumens')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenggat_waktu')
                    ->label('Tenggat Waktu')
                    ->dateTime('d M Y H:i')
                    ->color(
                        fn($record) =>
                        now()->lte($record->tenggat_waktu)
                            ? 'success'
                            :   'danger'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->formatStateUsing(fn($state) => strip_tags($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu'  => 'warning',
                        'disetujui' => 'success',
                        'ditolak'   => 'danger',
                        default     => 'secondary',
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
