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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\FormatFile;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Tabs;

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
                Forms\Components\FileUpload::make('path')
                    ->label('File Dokumen (Upload file sesuai template)')
                    ->required(fn(string $context) => $context === 'create')
                    ->storeFiles(false)
                    ->disk('local')
                    ->directory('temp')
                    ->maxSize(function () {
                        $dokumen      = $this->getOwnerRecord();
                        $jenisDokumen = $dokumen?->jenisDokumen;
                        return $jenisDokumen?->maksimal_ukuran ?? 20480;
                    })
                    ->acceptedFileTypes(function () {
                        $dokumen      = $this->getOwnerRecord();
                        $jenisDokumen = $dokumen?->jenisDokumen;
                        $mimeTypes    = FormatFile::whereIn('id', $jenisDokumen->format_file ?? [])->pluck('mime_types')->toArray();
                        return $mimeTypes;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');
        $isSubbagian    = $user->hasRole('subbagian');

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
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
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => $isSuperOrAdmin),
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
                    ->after(function ($record, $livewire) {
                        $owner = $this->getOwnerRecord();
                        if ($owner) {
                            $owner->status   = 'Revisi Menunggu Persetujuan';
                            $owner->komentar = '';
                            $owner->save();
                        }
                    })
                    ->createAnother(false)
                    ->visible(function () use ($user, $isSuperOrAdmin) {
                        if ($isSuperOrAdmin) return true;
                        $dokumen      = $this->getOwnerRecord();
                        $jenisDokumen = $dokumen->jenisDokumen;
                        if ($dokumen->fileDokumens()->count() >= $jenisDokumen->batas_unggah) return false;
                        $aksesPeranDokumen = $user->roles->pluck('id')->intersect($jenisDokumen->roles->pluck('id'));
                        return $aksesPeranDokumen->isNotEmpty();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('file-dokumen.unduh', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record?->path) && Storage::disk('local')->exists($record->path)),

                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->button()
                    ->infolist(function ($record) {
                        $formatUserInfo = function ($user, $tanggal) {
                            $bagian    = $user?->subbagian?->bagian?->nama;
                            $subbagian = $user?->subbagian?->nama;
                            $parts     = [
                                $user?->name,
                                $user?->nip ? 'NIP: ' . $user->nip                            : null,
                                $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                                $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                            ];
                            return implode(' | ', array_filter($parts));
                        };

                        return [
                            Tabs::make('Tab')
                                ->tabs([
                                    Tabs\Tab::make('Utama')
                                        ->schema([
                                            TextEntry::make('nama')->label('Nama')
                                                ->state($record->nama),

                                            TextEntry::make('tipe')->label('Tipe')
                                                ->state($record->tipe),

                                            TextEntry::make('ukuran')
                                                ->label('Ukuran')
                                                ->state(number_format(($record->ukuran ?? 0) / 1024, 2) . ' KB'),
                                        ]),

                                    Tabs\Tab::make('Riwayat Aktivitas')
                                        ->schema([
                                            TextEntry::make('pembuat.name')
                                                ->label('Dibuat Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembuat, $record->dibuat_pada)),

                                            TextEntry::make('pembaru.name')
                                                ->label('Diperbarui Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembaru, $record->diperbarui_pada)),

                                            TextEntry::make('penghapus.name')
                                                ->label('Dihapus Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->penghapus, $record->dihapus_pada)),

                                            TextEntry::make('pemulih.name')
                                                ->label('Dipulihkan Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pemulih, $record->dipulihkan_pada)),
                                        ])
                                        ->columns(2),
                                ]),
                        ];
                    }),

                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning'),
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
