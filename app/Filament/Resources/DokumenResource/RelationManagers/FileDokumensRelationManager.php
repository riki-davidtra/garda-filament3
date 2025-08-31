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
use Illuminate\Support\Facades\Auth;

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
                    ->maxSize(function () {
                        $dokumen = $this->getOwnerRecord();
                        $jenis   = $dokumen?->jenisDokumen;
                        return $jenis?->maksimal_ukuran ?? 20480;
                    })
                    ->acceptedFileTypes(function () {
                        $dokumen = $this->getOwnerRecord();
                        $jenis   = $dokumen?->jenisDokumen;
                        if (! $jenis || empty($jenis->format_file)) {
                            return [];
                        }
                        return \App\Models\FormatFile::whereIn('id', $jenis->format_file)->pluck('mime_types')->toArray();
                    })
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex flex-col'])
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->id) {
                            $component->hintAction(
                                \Filament\Forms\Components\Actions\Action::make('unduh')
                                    ->label('Unduh')
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
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');
        $isSubbagian    = $user->hasRole('subbagian');

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
                    ->description(function ($record) {
                        $user      = $record->pembuat;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dibuat_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Diperbarui Oleh')
                    ->description(function ($record) {
                        $user      = $record->pembaru;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->diperbarui_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghapus.name')
                    ->label('Dihapus Oleh')
                    ->description(function ($record) {
                        $user      = $record->penghapus;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dihapus_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemulih.name')
                    ->label('Dipulihkan Oleh')
                    ->description(function ($record) {
                        $user      = $record->pemulih;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dipulihkan_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
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
                return $current < $batas ? "Dokumen ini sudah menggunakan {$current} dari {$batas} kesempatan unggah file." : "Kesempatan unggah file sudah habis. Anda telah mencapai batas maksimal ({$batas} file).";
            })
            ->headerActions([
                // Ditampilkan aksi jika tidak lewat batas unggah, user Super Admin/Admin, perencana atau memiliki role yang sesuai dengan jenis dokumen
                Tables\Actions\CreateAction::make()
                    ->label('Unggah File Dokumen')
                    ->modalHeading('Unggah File Dokumen')
                    ->mutateFormDataUsing(fn(array $data) => $this->handleEncryptedUpload($data))
                    ->after(function ($record, $livewire) {
                        $owner = $this->getOwnerRecord();
                        if ($owner) {
                            $owner->status = 'Revisi Menunggu Persetujuan';
                            $owner->save();
                        }
                    })
                    ->createAnother(false)
                    ->visible(function () {
                        $dokumen = $this->getOwnerRecord();
                        $jenis   = $dokumen?->jenisDokumen;
                        return $dokumen && $jenis
                            && $dokumen->fileDokumens()->count() < $jenis->batas_unggah;
                    })
                    ->visible(function () use ($user, $isSuperOrAdmin) {
                        if ($isSuperOrAdmin) return true;
                        $dokumen = $this->getOwnerRecord();
                        if (!$dokumen) return false;
                        $jenisDokumen = $dokumen->jenisDokumen;
                        if (!$jenisDokumen) return false;
                        if ($dokumen->fileDokumens()->count() >= $jenisDokumen->batas_unggah) {
                            return false;
                        }
                        return $user && $jenisDokumen->roles && $user->roles->pluck('id')->intersect($jenisDokumen->roles->pluck('id'))->isNotEmpty();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
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
            $fileName    = $namaDokumen . ' - ' . now()->format('d-m-Y') . ' (v' . $versi . ')';
            $extension   = $file->getClientOriginalExtension();
            $path        = "file-dokumen/{$fileName}.{$extension}";

            Storage::disk('local')->put($path, encrypt(file_get_contents($file->getRealPath())));

            $data['path']   = $path;
            $data['nama']   = $fileName . '.' . $extension;
            $data['tipe']   = $file->getMimeType();
            $data['ukuran'] = $file->getSize();

            @unlink($file->getRealPath());
        }

        unset($data['file_temp']);

        return $data;
    }
}
