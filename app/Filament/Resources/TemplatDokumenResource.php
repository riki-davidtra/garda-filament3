<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplatDokumenResource\Pages;
use App\Filament\Resources\TemplatDokumenResource\RelationManagers;
use App\Models\TemplatDokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplatDokumenResource extends Resource
{
    protected static ?string $model = TemplatDokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Panduan & Bantuan';
    protected static ?string $navigationLabel  = 'Templat Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Templat Dokumen';
    protected static ?string $modelLabel       = 'Templat Dokumen';
    protected static ?int $navigationSort      = 54;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    }),

                Forms\Components\Repeater::make('fileTemplatDokumens')
                    ->label('File Templat Dokumen')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('path')
                            ->label('File')
                            ->nullable()
                            ->disk('public')
                            ->directory('file-templat-dokumen')
                            ->enableOpen()
                            ->enableDownload()
                            ->maxSize(20480),
                    ])
                    ->columnSpanFull()
                    ->itemLabel(fn(array $state): ?string => $state['nama_file'] ?? null)
                    ->addActionLabel('Tambah File Templat Dokumen')
                    ->minItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('jenisDokumen.nama')
                    ->label('Jenis Dokumen')
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
            'index' => Pages\ListTemplatDokumens::route('/'),
            'create' => Pages\CreateTemplatDokumen::route('/create'),
            // 'edit' => Pages\EditTemplatDokumen::route('/{record}/edit'),
        ];
    }
}
