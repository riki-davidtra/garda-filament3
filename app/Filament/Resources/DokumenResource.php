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
use App\Models\JenisDokumen;
use App\Models\FormatFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DokumenResource extends Resource
{
    protected static ?string $model = Dokumen::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user           = Auth::user();
        $jenisDokumenId = request()->query('jenis_dokumen_id');

        return $user && $jenisDokumenId && JenisDokumen::where('id', $jenisDokumenId)->exists();
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');

        return $form
            ->schema([
                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->hiddenOn('create')
                    ->disabled(!$isSuperOrAdmin),
                Forms\Components\Select::make('subbagian_id')
                    ->label('Subbagian')
                    ->required()
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
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->bagian->nama} - {$record->nama}")
                    ->hidden(fn() => $form->getOperation() === 'create')
                    ->disabled(!$isSuperOrAdmin),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Dokumen')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->helperText('Contoh: [RKA Perubahan] - [Rumah Tangga] - [Urusan Dalam]'),
                Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->options(fn() => array_combine(range(date('Y'), 2020), range(date('Y'), 2020)))
                    ->default(date('Y')),
                Forms\Components\Select::make('subkegiatan_id')
                    ->label('Subkegiatan')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('subkegiatan', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    }),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->maxLength(3000)
                    ->columnSpanFull(),
                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Menunggu Persetujuan'        => 'Menunggu Persetujuan',
                        'Diterima'                    => 'Diterima',
                        'Ditolak'                     => 'Ditolak',
                        'Revisi Menunggu Persetujuan' => 'Revisi Menunggu Persetujuan',
                        'Revisi Diterima'             => 'Revisi Diterima',
                        'Revisi Ditolak'              => 'Revisi Ditolak',
                    ])
                    ->default('Menunggu Persetujuan')
                    ->hiddenOn('create')
                    ->disabled(!$isSuperOrAdmin && !$isPerencana),
                Forms\Components\RichEditor::make('komentar')
                    ->label('Komentar')
                    ->nullable()
                    ->maxLength(3000)
                    ->columnSpanFull()
                    ->hiddenOn('create')
                    ->disabled(!$isSuperOrAdmin && !$isPerencana),

                Forms\Components\Repeater::make('fileDokumens')
                    ->label('')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('file_temp')
                            ->label('File Dokumen (Upload file sesuai template)')
                            ->required(fn(string $context) => $context === 'create')
                            ->storeFiles(false)
                            ->disk('local')
                            ->directory('temp')
                            ->maxSize(20480)
                            ->acceptedFileTypes(function ($get, $livewire) {
                                if (!($dokumen = JenisDokumen::find($livewire->jenis_dokumen_id))?->format_file) {
                                    return [];
                                }
                                return FormatFile::whereIn('id', $dokumen->format_file)->pluck('mime_types')->toArray();
                            })
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record, $livewire) {
                        if (!empty($data['file_temp']) && $data['file_temp'] instanceof TemporaryUploadedFile) {
                            $file = $data['file_temp'];

                            $owner       = $record ?? $livewire->getMountedActionRecord();
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
                    })
                    ->defaultItems(1)
                    ->maxItems(1)
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->columnSpanFull()
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire) {
                $user           = Auth::user();
                $jenisDokumenId = $livewire->jenis_dokumen_id;
                if ($jenisDokumenId) {
                    $query->where('jenis_dokumen_id', $jenisDokumenId);
                }
                if (!$user->hasAnyRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('subbagian_id', $user->subbagian_id);
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subkegiatan.nama')
                    ->label('Subkegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subbagian.nama')
                    ->label('Subbagian')
                    ->formatStateUsing(
                        fn($record) =>
                        "{$record->subbagian?->bagian?->nama} - {$record->subbagian?->nama}"
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu Persetujuan'        => 'warning',
                        'Diterima'                    => 'success',
                        'Ditolak'                     => 'danger',
                        'Revisi Menunggu Persetujuan' => 'warning',
                        'Revisi Diterima'             => 'success',
                        'Revisi Ditolak'              => 'danger',
                        default                       => 'secondary',
                    })
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
                Tables\Filters\SelectFilter::make('subbagian_id')
                    ->label('Subbagian')
                    ->relationship('subbagian', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subkegiatan_id')
                    ->label('Subkegiatan')
                    ->relationship('subkegiatan', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('tahun')
                    ->form([
                        Forms\Components\TextInput::make('tahun')
                            ->label('Tahun')
                            ->numeric()
                            ->maxLength(4),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['tahun'], fn($q, $tahun) => $q->where('tahun', $tahun));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['tahun'] ? 'Tahun: ' . $data['tahun'] : null;
                    }),
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => Auth::user()->hasAnyRole(['Super Admin', 'admin'])),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(function ($record) {
                        $fileTerbaru = $record->fileDokumens()->latest()->first();
                        return $fileTerbaru ? route('file-dokumen.unduh', $fileTerbaru->id) : '#';
                    })
                    ->visible(function ($record) {
                        $fileTerbaru = $record->fileDokumens()->latest()->first();
                        return $fileTerbaru
                            && $fileTerbaru->path
                            && Storage::disk('local')->exists($fileTerbaru->path);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->label('Detail Dokumen')
                    ->url(fn($record) => route('filament.admin.resources.dokumens.edit', [
                        'record'           => $record->uuid,
                        'jenis_dokumen_id' => request()->query('jenis_dokumen_id'),
                    ]))
                    ->visible(function ($record) {
                        $user         = Auth::user();
                        $jenisDokumen = $record->jenisDokumen;
                        return $user && $jenisDokumen && $user->roles->pluck('id')->intersect($jenisDokumen->roles->pluck('id'))->isNotEmpty();
                    }),
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
