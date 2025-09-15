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
use App\Models\JadwalDokumen;
use App\Models\FormatFile;
use App\Models\Bagian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Services\DokumenService;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;

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

    protected static ?JenisDokumen $jenisDokumen = null;
    public static function getJenisDokumen($id = null): ?JenisDokumen
    {
        if (!static::$jenisDokumen && $id) {
            static::$jenisDokumen = JenisDokumen::find($id);
        }
        return static::$jenisDokumen;
    }

    protected static ?JadwalDokumen $jadwalDokumen = null;
    public static function getJadwalDokumen($jenisDokumenId = null): ?JadwalDokumen
    {
        if (!static::$jadwalDokumen && $jenisDokumenId) {
            static::$jadwalDokumen = JadwalDokumen::where('jenis_dokumen_id', $jenisDokumenId)->first();
        }
        return static::$jadwalDokumen;
    }

    public static function form(Form $form): Form
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');

        return $form
            ->schema([
                Forms\Components\Hidden::make('jenis_dokumen_id')
                    ->default(request()->query('jenis_dokumen_id'))
                    ->required(),

                Forms\Components\Hidden::make('jadwal_dokumen_id')
                    ->default(request()->query('jadwal_dokumen_id'))
                    ->required(),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->helperText('Contoh: RKA Perubahan - Rumah Tangga - Urusan Dalam'),

                // Set default dan disabled jika ada data dari jadwal dokumen dan simpan db di handle dari observer 
                Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->options(fn() => array_combine(
                        range(2025, date('Y') + 1),
                        range(2025, date('Y') + 1)
                    ))
                    ->default(function ($get) {
                        $jadwalDokumen = self::getJadwalDokumen($get('jenis_dokumen_id'));
                        return $jadwalDokumen?->tahun ?? null;
                    })
                    ->disabled(function ($get) {
                        $jadwalDokumen           = self::getJadwalDokumen($get('jenis_dokumen_id'));
                        return $jadwalDokumen?->tahun !== null;
                    }),

                // Tampilkan, set default dan disabled jika ada data dari jadwal dokumen dan simpan db di handle dari observer 
                Forms\Components\Select::make('periode')
                    ->label('Periode')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ])
                    ->default(function ($get) {
                        $jadwalDokumen = self::getJadwalDokumen($get('jenis_dokumen_id'));
                        return $jadwalDokumen?->periode ?? null;
                    })
                    ->disabled(function ($get) {
                        $jadwalDokumen             = self::getJadwalDokumen($get('jenis_dokumen_id'));
                        return $jadwalDokumen?->periode !== null;
                    })
                    ->visible(function ($get) {
                        $jenisDokumen = self::getJenisDokumen($get('jenis_dokumen_id'));
                        return $jenisDokumen?->mode_periode;
                    }),

                // Tampilkan jika memiliki akses mode pada dokumen ini
                Forms\Components\Select::make('subkegiatan_id')
                    ->label('Subkegiatan')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('subkegiatan', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->visible(function ($get) {
                        $jenisDokumen = self::getJenisDokumen($get('jenis_dokumen_id'));
                        return $jenisDokumen?->mode_subkegiatan;
                    }),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->maxLength(3000)
                    ->columnSpanFull(),

                // Tampilkan repeater unggah file dokumen hanya di create
                Forms\Components\Repeater::make('files')
                    ->label(false)
                    ->relationship('files')
                    ->schema([
                        Forms\Components\Hidden::make('tag')->default('dokumen'),

                        Forms\Components\FileUpload::make('path')
                            ->label('File (Upload file sesuai template)')
                            ->nullable()
                            ->storeFiles(false)
                            ->disk('local')
                            ->directory('temp')
                            ->maxSize(function ($get) {
                                $jenisDokumen = self::getJenisDokumen($get('jenis_dokumen_id'));
                                return $jenisDokumen?->maksimal_ukuran ?? 20480;
                            })
                            ->acceptedFileTypes(function ($get) {
                                $jenisDokumen = self::getJenisDokumen($get('jenis_dokumen_id'));
                                $mimeTypes    = FormatFile::whereIn('id', $jenisDokumen?->format_file ?? [])->pluck('mime_types')->toArray();
                                return $mimeTypes;
                            })
                            ->columnSpanFull(),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->columns(2)
                    ->columnSpanFull()
                    ->visibleOn('create'),

                // Tampilkan relasi data jika roles Super Admin/Admin
                // Forms\Components\Fieldset::make('Relasi Data')
                //     ->schema([
                //         Forms\Components\Select::make('jenis_dokumen_id')
                //             ->label('Jenis Dokumen')
                //             ->required()
                //             ->searchable()
                //             ->preload()
                //             ->relationship('jenisDokumen', 'nama', function ($query) {
                //                 $query->orderBy('nama', 'asc');
                //             })
                //             ->hiddenOn('create'),

                //         Forms\Components\Select::make('jadwal_dokumen_id')
                //             ->label('Kode Jadwal')
                //             ->required()
                //             ->searchable()
                //             ->preload()
                //             ->relationship('jadwalDokumen', 'kode', function ($query, $get) {
                //                 $jenisId = $get('jenis_dokumen_id');
                //                 if ($jenisId) {
                //                     $query->where('jenis_dokumen_id', $jenisId);
                //                 }
                //                 $query->orderBy('kode', 'asc');
                //             })
                //             ->hiddenOn('create'),

                //         Forms\Components\Select::make('subbagian_id')
                //             ->label('Subbagian')
                //             ->nullable()
                //             ->searchable()
                //             ->preload()
                //             ->relationship(
                //                 'subbagian',
                //                 'nama',
                //                 fn($query) => $query->with('bagian')->orderBy(Bagian::select('nama')->whereColumn('bagians.id', 'subbagians.bagian_id'))->orderBy('nama')
                //             )
                //             ->getOptionLabelFromRecordUsing(fn($record) => "{$record->bagian->nama} - {$record->nama}")
                //             ->hiddenOn('create'),
                //     ])
                //     ->hiddenOn('create')
                //     ->visible($isSuperOrAdmin),

                // Tampilkan jika mode status nya aktif pada dokumen ini
                Forms\Components\Fieldset::make('Status Dokumen')
                    ->schema([
                        Forms\Components\Select::make('status')
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
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('komentar', null);
                            })
                            ->hiddenOn('create')
                            ->disabled(!$isSuperOrAdmin && !$isPerencana),

                        Forms\Components\Textarea::make('komentar')
                            ->label('Komentar')
                            ->nullable()
                            ->maxLength(3000)
                            ->columnSpanFull()
                            ->disabled(!$isSuperOrAdmin && !$isPerencana),
                    ])
                    ->hiddenOn('create')
                    ->visible(function ($get) use ($isSuperOrAdmin) {
                        $jenisDokumen = self::getJenisDokumen($get('jenis_dokumen_id'));
                        return $isSuperOrAdmin && $jenisDokumen?->mode_status;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');

        return $table
            // Filter query: batasi data berdasarkan jenis dokumen dan subbagian jika bukan Super Admin/Admin atau Perencana dan memiliki akses role pada dokumen ini
            ->modifyQueryUsing(function (Builder $query, $livewire) use ($user, $isSuperOrAdmin, $isPerencana) {
                $jenisDokumenId = $livewire->jenis_dokumen_id;
                if ($jenisDokumenId) {
                    $query->where('jenis_dokumen_id', $jenisDokumenId);
                }

                $jenisDokumen      = self::getJenisDokumen($jenisDokumenId);
                $aksesPeranDokumen = $user->roles->pluck('id')->intersect($jenisDokumen?->roles->pluck('id'));
                if (!$isSuperOrAdmin && !$isPerencana && $aksesPeranDokumen->isNotEmpty()) {
                    $query->where('subbagian_id', $user->subbagian_id);
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subkegiatan.nama')
                    ->label('Subkegiatan')
                    ->searchable()
                    ->sortable()
                    ->visible(function ($livewire) {
                        $jenisDokumen = self::getJenisDokumen($livewire->jenis_dokumen_id);
                        return $jenisDokumen?->mode_subkegiatan;
                    }),

                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable()
                    ->sortable()
                    ->visible(function ($livewire) {
                        $jenisDokumen = self::getJenisDokumen($livewire->jenis_dokumen_id);
                        return $jenisDokumen?->mode_periode;
                    }),

                // Ditampilkan untuk Super Admin/Admin, atau tampil jika jenis dokumen terkait memiliki role 'subbagian'
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu Persetujuan'        => 'warning',
                        'Diterima'                    => 'success',
                        'Ditolak'                     => 'danger',
                        'Revisi Menunggu Persetujuan' => 'warning',
                        'Revisi Diterima'             => 'success',
                        'Revisi Ditolak'              => 'danger',
                        default                       => 'gray',
                    })
                    ->searchable()
                    ->sortable()
                    ->visible(function ($livewire) {
                        $jenisDokumen = self::getJenisDokumen($livewire->jenis_dokumen_id);
                        return $jenisDokumen?->mode_status;
                    }),
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

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => $isSuperOrAdmin),
            ])
            ->actions([
                // Tampilkan aksi hanya jika ada file dokumen terbaru yang tersedia di storage
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(function ($record) {
                        $fileTerbaru = $record->files()->latest()->first();
                        return $fileTerbaru ? route('file-dokumen.unduh', $fileTerbaru->id) : '#';
                    })
                    ->visible(function ($record) {
                        $fileTerbaru = $record->files()->latest()->first();
                        return $fileTerbaru && $fileTerbaru->path && Storage::disk('local')->exists($fileTerbaru->path);
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->button()
                    ->url(fn($record, $livewire) => route('filament.admin.resources.dokumens.view', [
                        'record'           => $record->uuid,
                        'jenis_dokumen_id' => $livewire->jenis_dokumen_id,
                    ])),

                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning')
                    ->url(fn($record, $livewire) => route('filament.admin.resources.dokumens.edit', [
                        'record'           => $record->uuid,
                        'jenis_dokumen_id' => $livewire->jenis_dokumen_id,
                    ]))
                    ->visible(function ($record) use ($user, $isSuperOrAdmin) {
                        if ($isSuperOrAdmin) return true;
                        $aksesPeran = $user->roles->pluck('id')->intersect($record->jenisDokumen?->roles->pluck('id') ?? collect());
                        return $aksesPeran->isNotEmpty();
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\FilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDokumens::route('/'),
            'create' => Pages\CreateDokumen::route('/create'),
            'edit'   => Pages\EditDokumen::route('/{record}/edit'),
            'view'   => Pages\ViewDokumen::route('/{record}'),
        ];
    }
}
