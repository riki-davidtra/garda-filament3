<?php

namespace App\Filament\Pages;

use App\Models\JadwalDokumen;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class UnggahDokumen extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Dokumen';
    protected static ?string $navigationLabel = 'Unggah Dokumen';
    protected static ?string $title           = 'Unggah Dokumen';
    protected static ?int $navigationSort     = 31;

    protected static string $view = 'filament.pages.unggah-dokumen';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('create Dokumen', \App\Models\Dokumen::class) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user();
                $query = JadwalDokumen::query()->with('jenisDokumen.templatDokumen')->where('aktif', true);
                if (!$user->hasAnyRole(['Super Admin', 'admin'])) {
                    $query->whereHas('jenisDokumen.roles', function ($q) use ($user) {
                        $q->where('role_id', $user->roles->pluck('id'));
                    });
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ViewColumn::make('icon_jenis_dokumen')
                        ->view('filament.components.column-icon-jenis-dokumen')
                        ->alignCenter(),
                    Tables\Columns\TextColumn::make('jenisDokumen.nama')
                        ->label('Jenis Dokumen')
                        ->alignCenter()
                        ->weight('bold')
                        ->size('large')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('waktu_unggah_mulai')
                            ->label('Waktu Unggah')
                            ->placeholder('Waktu tidak ditentukan')
                            ->formatStateUsing(function ($record) {
                                $mulai   = $record->waktu_unggah_mulai->format('d-m-Y H:i');
                                $selesai = $record->waktu_unggah_selesai->format('d-m-Y H:i');
                                return "{$mulai} → {$selesai}";
                            })
                            ->color(fn($record) => match (true) {
                                $record->waktu_unggah_mulai   && now()->lt($record->waktu_unggah_mulai)   => 'gray',
                                $record->waktu_unggah_selesai && now()->gt($record->waktu_unggah_selesai) => 'danger',
                                default                                     => 'success',
                            })
                            ->alignCenter(),
                    ]),
                    Tables\Columns\ViewColumn::make('unduh-template')
                        ->label('Unduh Template')
                        ->view('filament.components.column-unduh-template')
                        ->alignCenter(),
                ]),
            ])
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
            ])
            ->actions([
                Action::make('unggah')
                    ->label('Unggah')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->button()
                    ->color('primary')
                    ->url(fn($record) => route('filament.admin.resources.dokumens.create', [
                        'jenis_dokumen_id'  => $record->jenis_dokumen_id,
                        'jadwal_dokumen_id' => $record->id,
                    ]))
                    ->visible(
                        fn($record) => $record->waktu_unggah_mulai && $record->waktu_unggah_selesai &&  now()->between($record->waktu_unggah_mulai, $record->waktu_unggah_selesai)
                    )
                    ->disabled(
                        fn($record) => !$record->waktu_unggah_mulai || !$record->waktu_unggah_selesai
                    )
                    ->extraAttributes(['class' => 'mx-auto flex justify-center']),
            ])
            ->filters([
                SelectFilter::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->relationship('jenisDokumen', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->bulkActions([]);
    }
}
