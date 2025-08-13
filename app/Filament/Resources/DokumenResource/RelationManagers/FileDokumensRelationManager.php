<?php

namespace App\Filament\Resources\DokumenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class FileDokumensRelationManager extends RelationManager
{
    protected static string $relationship = 'fileDokumens';
    protected static ?string $title = 'Daftar File Dokumen';
    protected static ?string $label = 'File Dokumen';
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public function form(Form $form): Form
    {
        $isDisabled = !auth()->user()->hasRole(['Super Admin', 'admin', 'perencana']);

        return $form
            ->schema([
                Forms\Components\Select::make('subbagian_id')
                    ->label('Subbagian')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->relationship('subbagian', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->hiddenOn('create')
                    ->disabled(),
                Forms\Components\FileUpload::make('file_temp')
                    ->label('File')
                    ->required(fn(string $context) => $context === 'create')
                    ->storeFiles(false)
                    ->disk('local')
                    ->directory('temp')
                    ->maxSize(2048)
                    ->columnSpanFull()
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
                    ])
                    ->extraAttributes(['class' => 'flex flex-col'])
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->id) {
                            $component->hintAction(
                                \Filament\Forms\Components\Actions\Action::make('unduh')
                                    ->label('Unduh File')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->url(route('file-dokumen.unduh', $record->id))
                                    ->openUrlInNewTab()
                            );
                        }
                    }),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama File')
                    ->columnSpanFull()
                    ->hiddenOn('create')
                    ->disabled(),
                Forms\Components\TextInput::make('tipe')
                    ->label('Tipe')
                    ->hiddenOn('create')
                    ->disabled(),
                Forms\Components\TextInput::make('ukuran')
                    ->label('Ukuran')
                    ->formatStateUsing(fn($state) => number_format($state / 1024, 2) . ' KB')
                    ->hiddenOn('create')
                    ->disabled(),
                Forms\Components\RichEditor::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->columnSpanFull()
                    ->maxLength(3000)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('dokumen/keterangan')
                    ->hiddenOn('create')
                    ->disabled($isDisabled),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->inline()
                    ->options([
                        'baru'      => 'Baru',
                        'revisi'    => 'Revisi',
                        'terlambat' => 'Terlambat',
                        'selesai'   => 'Selesai',
                    ])
                    ->default('baru')
                    ->hiddenOn('create')
                    ->disabled($isDisabled),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (!$user->hasRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('subbagian_id', $user->subbagian_id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subbagian.nama')
                    ->label('Subbagian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama File')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baru'      => 'info',
                        'revisi'    => 'warning',
                        'terlambat' => 'danger',
                        'selesai'   => 'success',
                        default     => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
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
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Diperbarui Oleh')
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
                Tables\Columns\TextColumn::make('deleter.name')
                    ->label('Dihapus Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restorer.name')
                    ->label('Dipulihkan Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restored_at')
                    ->label('Dipulihkan Pada')
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn(array $data) => $this->handleEncryptedUpload($data)),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('file-dokumen.unduh', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn(array $data) => $this->handleEncryptedUpload($data)),
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

    private function handleEncryptedUpload(array $data): array
    {
        if (!empty($data['file_temp']) && $data['file_temp'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $file = $data['file_temp'];

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueCode = \Illuminate\Support\Str::padLeft(mt_rand(0, 9999), 6, '0');
            $path = 'file-dokumen/' . $originalName . '-' . $uniqueCode . '.' . $extension;

            $encryptedContent = encrypt(file_get_contents($file->getRealPath()));

            \Illuminate\Support\Facades\Storage::disk('local')->put($path, $encryptedContent);

            $data['path'] = $path;
        }

        unset($data['file_temp']);

        return $data;
    }
}
