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
use App\Models\JenisDokumen;

class DokumenResource extends Resource
{
    protected static ?string $model = Dokumen::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        $jenisDokumenId = request()->query('jenis_dokumen_id');

        return $user
            && $user->roles->isNotEmpty()
            && $jenisDokumenId
            && JenisDokumen::where('id', $jenisDokumenId)
            ->whereHas('roles', fn($query) => $query->whereIn('roles.id', $user->roles->pluck('id')))
            ->exists();
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        $isSuperOrAdmin = $user->hasRole(['Super Admin', 'admin']);
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
                        fn($query) => $query
                            ->with('bagian')
                            ->orderBy(
                                \App\Models\Bagian::select('nama')
                                    ->whereColumn('bagians.id', 'subbagians.bagian_id')
                            )
                            ->orderBy('nama')
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($record) => "{$record->nama} - {$record->bagian->nama}"
                    )
                    ->hidden(fn() => $form->getOperation() === 'create')
                    ->disabled(!$isSuperOrAdmin),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Dokumen')
                    ->required()
                    ->string()
                    ->helperText('Contoh: [Jenis Dokumen] - [Nama Bagian] - [Nama Subbagian]'),
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
                    ->label('File Dokumen')
                    ->relationship()
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
                            ])
                            ->helperText('Maks. 20MB. Format: PDF, Word, Excel, PowerPoint.')
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record, $livewire) {
                        if (!empty($data['file_temp']) && $data['file_temp'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $file = $data['file_temp'];

                            $owner       = $record ?? $livewire->getMountedActionRecord();
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
                $jenisDokumenId = $livewire->jenis_dokumen_id ?? null;
                $user           = Auth::user();

                if ($jenisDokumenId) {
                    $query->where('jenis_dokumen_id', $jenisDokumenId);
                }

                if (!$user->hasRole(['Super Admin', 'admin', 'perencana'])) {
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
                        "{$record->subbagian?->nama} - {$record->subbagian?->bagian?->nama}"
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
                    ->placeholder('-')
                    ->description(
                        fn(Dokumen $record): string =>
                        'NIP: ' . ($record->pembuat?->nip ?? '-') . ($record->dibuat_pada ? ' | ' . $record->dibuat_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Diperbarui Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(Dokumen $record): string =>
                        'NIP: ' . ($record->pembaru?->nip ?? '-') . ($record->diperbarui_pada ? ' | ' . $record->diperbarui_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghapus.name')
                    ->label('Dihapus Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(Dokumen $record): string =>
                        'NIP: ' . ($record->penghapus?->nip ?? '-') . ($record->dihapus_pada ? ' | ' . $record->dihapus_pada : '')
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemulih.name')
                    ->label('Dipulihkan Oleh')
                    ->placeholder('-')
                    ->description(
                        fn(Dokumen $record): string =>
                        'NIP: ' . ($record->pemulih?->nip ?? '-') . ($record->dipulihkan_pada ? ' | ' . $record->dipulihkan_pada : '')
                    )
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('unduh_file')
                    ->label('Unduh File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(function ($record) {
                        $fileTerbaru = $record->fileDokumens()->latest()->first();
                        return $fileTerbaru ? route('file-dokumen.unduh', $fileTerbaru->id) : '#';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->fileDokumens()->exists()),
                Tables\Actions\EditAction::make()
                    ->label('Detail Dokumen')
                    ->url(fn($record) => route('filament.admin.resources.dokumens.edit', [
                        'record'           => $record->uuid,
                        'jenis_dokumen_id' => request()->query('jenis_dokumen_id'),
                    ])),
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
