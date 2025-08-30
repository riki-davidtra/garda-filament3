<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatAktivitasResource\Pages;
use App\Filament\Resources\RiwayatAktivitasResource\RelationManagers;
use App\Models\RiwayatAktivitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RiwayatAktivitasResource extends Resource
{
    protected static ?string $model = RiwayatAktivitas::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clock';
    protected static ?string $navigationLabel  = 'Riwayat Aktivitas';
    protected static ?string $pluralModelLabel = 'Daftar Riwayat Aktivitas';
    protected static ?string $modelLabel       = 'Riwayat Aktivitas';
    protected static ?int $navigationSort      = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->visibleOn('view')
                    ->disabled(),

                Forms\Components\TextInput::make('aksi')
                    ->label('Aksi')
                    ->visibleOn('view')
                    ->disabled(),

                Forms\Components\TextInput::make('jenis_data')
                    ->label('Jenis Data')
                    ->visibleOn('view')
                    ->disabled(),

                Forms\Components\Textarea::make('detail_data')
                    ->label('Detail Data')
                    ->visibleOn('view')
                    ->columnSpanFull()
                    ->disabled(),

                Forms\Components\TextInput::make('ip')
                    ->label('Alamat IP')
                    ->visibleOn('view')
                    ->disabled(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Waktu Dibuat')
                    ->disabled()
                    ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')
                    ->badge()
                    ->color(function ($record) {
                        return match ($record->aksi) {
                            'masuk'          => 'primary',
                            'keluar'         => 'gray',
                            'buat'           => 'success',
                            'ubah'           => 'warning',
                            'hapus'          => 'danger',
                            'hapus permanen' => 'danger',
                            'pulihkan'       => 'info',
                            default          => 'gray',
                        };
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_data')
                    ->label('Jenis Data')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip')
                    ->label('Alamat IP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d-m-Y H:i')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->default(now()->format('Y-m-d')),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->default(now()->format('Y-m-d')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn($q, $dari) => $q->whereDate('created_at', '>=', $dari))
                            ->when($data['sampai'] ?? null, fn($q, $sampai) => $q->whereDate('created_at', '<=', $sampai));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (($data['dari'] ?? null) && ($data['sampai'] ?? null)) {
                            return "Dari {$data['dari']} sampai {$data['sampai']}";
                        } elseif ($data['dari'] ?? null) {
                            return "Dari {$data['dari']}";
                        } elseif ($data['sampai'] ?? null) {
                            return "Sampai {$data['sampai']}";
                        }
                        return null;
                    }),

                Tables\Filters\SelectFilter::make('aksi')->options([
                    'masuk'          => 'Masuk',
                    'keluar'         => 'Keluar',
                    'buat'           => 'Buat',
                    'ubah'           => 'Ubah',
                    'hapus'          => 'Hapus',
                    'hapus permanen' => 'Hapus Permanen',
                    'pulihkan'       => 'Pulihkan',
                ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRiwayatAktivitas::route('/'),
            // 'create' => Pages\CreateRiwayatAktivitas::route('/create'),
            // 'edit'   => Pages\EditRiwayatAktivitas::route('/{record}/edit'),
        ];
    }
}
