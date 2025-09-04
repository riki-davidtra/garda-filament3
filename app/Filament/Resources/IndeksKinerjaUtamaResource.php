<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndeksKinerjaUtamaResource\Pages;
use App\Filament\Resources\IndeksKinerjaUtamaResource\RelationManagers;
use App\Models\IndeksKinerjaUtama;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Storage;

class IndeksKinerjaUtamaResource extends Resource
{
    protected static ?string $model = IndeksKinerjaUtama::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Formulir';
    protected static ?string $navigationLabel  = 'Indeks Kinerja Utama (IKU)';
    protected static ?string $pluralModelLabel = 'Daftar Indeks Kinerja Utama (IKU)';
    protected static ?string $modelLabel       = 'Indeks Kinerja Utama (IKU)';
    protected static ?int $navigationSort      = 41;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('indikator_id')
                    ->label('Indikator')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('indikator', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, callable $get) {
                            return $rule
                                ->where('periode', $get('periode'))
                                ->where('tahun', $get('tahun'))
                                ->where('dibuat_oleh', Auth::id());
                        }
                    )
                    ->validationMessages([
                        'unique' => 'Indikator dan Periode untuk tahun ini sudah ada!',
                    ]),

                Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->options(fn() => array_combine(range(date('Y'), 2020), range(date('Y'), 2020)))
                    ->default(date('Y')),

                Forms\Components\Select::make('periode')
                    ->label('Periode')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options([
                        'Triwulan I'   => 'Triwulan I',
                        'Triwulan II'  => 'Triwulan II',
                        'Triwulan III' => 'Triwulan III',
                        'Triwulan IV'  => 'Triwulan IV',
                    ])
                    ->reactive(),

                Forms\Components\Fieldset::make('Data Bulanan')
                    ->schema(function (callable $get) {
                        $periode = $get('periode');

                        $mapping = [
                            'Triwulan I'   => ['Januari', 'Februari', 'Maret'],
                            'Triwulan II'  => ['April', 'Mei', 'Juni'],
                            'Triwulan III' => ['Juli', 'Agustus', 'September'],
                            'Triwulan IV'  => ['Oktober', 'November', 'Desember'],
                        ];

                        $months = $mapping[$periode] ?? [];

                        return collect($months)->map(
                            fn($bulan, $i) =>
                            Forms\Components\TextInput::make("nilai_bulan_" . ($i + 1))
                                ->label($bulan)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999)
                                ->default(0)
                        )->toArray();
                    })
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire) {
                $user = Auth::user();
                if (!$user->hasAnyRole(['Super Admin', 'admin', 'perencana'])) {
                    $query->where('dibuat_oleh', $user->id);
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('indikator.nama')
                    ->label('Indikator')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('perubahan_ke')
                    ->label('Perubahan ke')
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Diisi Oleh')
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
                    ->label('Direvisi Oleh')
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
            ->actions([
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('iku.unduh', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record)),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIndeksKinerjaUtamas::route('/'),
            // 'create' => Pages\CreateIndeksKinerjaUtama::route('/create'),
            // 'edit'   => Pages\EditIndeksKinerjaUtama::route('/{record}/edit'),
        ];
    }
}
