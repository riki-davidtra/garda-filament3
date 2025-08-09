<?php

namespace App\Filament\Resources\DokumenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FileDokumensRelationManager extends RelationManager
{
    protected static string $relationship = 'fileDokumens';
    protected static ?string $title = 'Daftar File Dokumen';
    protected static ?string $label = 'File Dokumen';
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->label('Unggah File')
                    ->required()
                    ->disk('public')
                    ->directory('file-dokumen')
                    ->openable()
                    ->downloadable()
                    ->maxSize(2048)
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $uniqueCode = uniqid();

                        return $originalName . '-' . $uniqueCode . '.' . $extension;
                    })
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                        'image/heic',
                        'image/heif',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama File')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => number_format($state / 1024, 2) . ' KB'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diunggah Oleh')
                    ->sortable()
                    ->searchable(),
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
                \Filament\Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
