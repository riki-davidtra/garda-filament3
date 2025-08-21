<?php

namespace App\Filament\Resources\DokumenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use App\Models\FileDokumen;

class FileDokumensRelationManager extends RelationManager
{
    protected static string $relationship = 'fileDokumens';
    protected static ?string $title       = 'Daftar File Dokumen';
    protected static ?string $label       = 'File Dokumen';
    protected static bool $canCreate      = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_temp')
                    ->label('File')
                    ->required(fn(string $context) => $context === 'create')
                    ->storeFiles(false)
                    ->disk('local')
                    ->directory('temp')
                    ->maxSize(20480)
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
                    ->helperText('Maks. 20MB. Format: PDF, Word, Excel, PowerPoint.')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex flex-col'])
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->id) {
                            $component->hintAction(
                                \Filament\Forms\Components\Actions\Action::make('unduh')
                                    ->label('Unduh File')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->url(function ($record) {
                                        $path = $record->path;
                                        return $path ? route('file-dokumen.unduh', $record->id) : '#';
                                    })
                                    ->visible(function ($record) {
                                        $path = $record->path;
                                        return $path && Storage::disk('local')->exists($path);
                                    })
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama File')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->formatStateUsing(fn($state) => number_format($state / 1024, 2) . ' KB')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dibuat_pada')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(FileDokumen $record): string =>
                        'NIP: ' . ($record->pembuat?->nip ?? '-') . ($record->dibuat_pada ? ' | ' . $record->dibuat_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Diperbarui Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(FileDokumen $record): string =>
                        'NIP: ' . ($record->pembaru?->nip ?? '-') . ($record->diperbarui_pada ? ' | ' . $record->diperbarui_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghapus.name')
                    ->label('Dihapus Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(FileDokumen $record): string =>
                        'NIP: ' . ($record->penghapus?->nip ?? '-') . ($record->dihapus_pada ? ' | ' . $record->dihapus_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemulih.name')
                    ->label('Dipulihkan Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(FileDokumen $record): string =>
                        'NIP: ' . ($record->pemulih?->nip ?? '-') . ($record->dipulihkan_pada ? ' | ' . $record->dipulihkan_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->description(function () {
                $dokumen = $this->getOwnerRecord();

                if (!$dokumen || !$dokumen->jenisDokumen) {
                    return 'Belum ada informasi jenis dokumen.';
                }

                $current = $dokumen->fileDokumens()->count();
                $batas   = $dokumen->jenisDokumen->batas_unggah;

                return $current < $batas
                    ? "Anda sudah menggunakan {$current} dari {$batas} kesempatan unggah file."
                    :      "Kesempatan unggah file sudah habis. Anda telah mencapai batas maksimal ({$batas} file).";
            })
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Unggah File Dokumen')
                    ->modalHeading('Unggah File Dokumen')
                    ->mutateFormDataUsing(fn(array $data) => $this->handleEncryptedUpload($data))
                    ->visible(function () {
                        $dokumen = $this->getOwnerRecord();
                        $jenis   = $dokumen?->jenisDokumen;
                        return $dokumen && $jenis
                            && $dokumen->fileDokumens()->count() < $jenis->batas_unggah;
                    })
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh_file')
                    ->label('Unduh File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(function ($record) {
                        $path = $record->path;
                        return $path ? route('file-dokumen.unduh', $record->id) : '#';
                    })
                    ->visible(function ($record) {
                        $path = $record->path;
                        return $path && Storage::disk('local')->exists($path);
                    })
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

            $owner       = $this->getOwnerRecord();
            $namaDokumen = $owner?->nama ?? 'dokumen';
            $versi       = ($owner?->fileDokumens()->count() ?? 0) + 1;
            $uniqueCode = \Illuminate\Support\Str::padLeft(mt_rand(0, 9999), 6, '0');
            $safeName    = \Illuminate\Support\Str::slug($namaDokumen) . "-v{$versi}" . "-{$uniqueCode}";
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-dokumen/{$safeName}.{$extension}";

            \Illuminate\Support\Facades\Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            $data['path']   = $path;
            $data['nama']   = $safeName . '.' . $extension;
            $data['tipe']   = $file->getMimeType();
            $data['ukuran'] = $file->getSize();

            @unlink($file->getRealPath());
        }

        unset($data['file_temp']);

        return $data;
    }
}
