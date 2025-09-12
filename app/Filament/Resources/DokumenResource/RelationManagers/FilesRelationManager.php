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
use Illuminate\Support\Facades\Storage;
use App\Models\FormatFile;
use Filament\Infolists;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';
    protected static ?string $title       = 'Daftar File Dokumen';
    protected static ?string $label       = 'File Dokumen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('tag')->default('dokumen'),

                Forms\Components\FileUpload::make('path')
                    ->label('File (Upload file sesuai template)')
                    ->required()
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

        return $table
            ->defaultSort('created_at', 'desc')
            ->description(function () {
                $dokumen = $this->getOwnerRecord();
                if (!$dokumen || !$dokumen->jenisDokumen) {
                    return 'Belum ada informasi jenis dokumen.';
                }
                $current = $dokumen->files()->count();
                $batas   = $dokumen->jenisDokumen->batas_unggah;
                return $current < $batas ? "Dokumen ini sudah menggunakan {$current} dari {$batas} kesempatan unggah file." : "Kesempatan unggah file sudah habis. Anda telah mencapai batas maksimal ({$batas} file).";
            })
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->state(function ($record) {
                        return sprintf(
                            '%s (v%s).%s',
                            $record->model?->nama,
                            $record->versi ?? 1,
                            $record->tipe
                        );
                    })
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
                Tables\Columns\TextColumn::make('versi')
                    ->label('Versi')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => $isSuperOrAdmin),
            ])
            ->headerActions([
                // Ditampilkan aksi jika tidak lewat batas unggah, user Super Admin/Admin, perencana atau memiliki role yang sesuai dengan jenis dokumen
                Tables\Actions\CreateAction::make()
                    ->label('Unggah')
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
                        if ($dokumen->files()->count() >= $jenisDokumen->batas_unggah) return false;
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
                    ->modalHeading('Detail File Dokumen')
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
                            Infolists\Components\Tabs::make('Tab')
                                ->tabs([
                                    Infolists\Components\Tabs\Tab::make('Utama')
                                        ->schema([
                                            Infolists\Components\TextEntry::make('nama')->label('Nama')
                                                ->state(function ($record) {
                                                    return sprintf(
                                                        '%s (v%s).%s',
                                                        $record->model?->nama,
                                                        $record->versi ?? 1,
                                                        $record->tipe
                                                    );
                                                }),

                                            Infolists\Components\TextEntry::make('tipe')->label('Tipe')
                                                ->state($record->tipe),

                                            Infolists\Components\TextEntry::make('ukuran')
                                                ->label('Ukuran')
                                                ->state(number_format(($record->ukuran ?? 0) / 1024, 2) . ' KB'),
                                        ]),

                                    Infolists\Components\Tabs\Tab::make('Riwayat Aktivitas')
                                        ->schema([
                                            Infolists\Components\TextEntry::make('pembuat.name')
                                                ->label('Dibuat Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembuat, $record->dibuat_pada)),

                                            Infolists\Components\TextEntry::make('pembaru.name')
                                                ->label('Diperbarui Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pembaru, $record->diperbarui_pada)),

                                            Infolists\Components\TextEntry::make('penghapus.name')
                                                ->label('Dihapus Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->penghapus, $record->dihapus_pada)),

                                            Infolists\Components\TextEntry::make('pemulih.name')
                                                ->label('Dipulihkan Oleh')
                                                ->placeholder('-')
                                                ->state($formatUserInfo($record->pemulih, $record->dipulihkan_pada)),
                                        ])
                                        ->columns(2),
                                ]),
                        ];
                    }),
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
