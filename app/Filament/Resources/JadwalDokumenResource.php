<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalDokumenResource\Pages;
use App\Filament\Resources\JadwalDokumenResource\RelationManagers;
use App\Models\JadwalDokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\JadwalDokumenService;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;

class JadwalDokumenResource extends Resource
{
    protected static ?string $model = JadwalDokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clock';
    protected static ?string $navigationGroup  = 'Data Master';
    protected static ?string $navigationLabel  = 'Jadwal Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Jadwal Dokumen';
    protected static ?string $modelLabel       = 'Jadwal Dokumen';
    protected static ?int $navigationSort      = 25;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->disabled()
                    ->hiddenOn('create'),

                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    }),

                Forms\Components\DateTimePicker::make('waktu_unggah_mulai')
                    ->label('Waktu Unggah Mulai')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('waktu_unggah_selesai')
                    ->label('Waktu Unggah Selesai')
                    ->nullable(),

                Forms\Components\Toggle::make('aktif')
                    ->nullable()
                    ->default(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenisDokumen.nama')
                    ->label('Jenis Dokumen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('waktu_unggah_mulai')
                    ->label('Waktu Unggah')
                    ->formatStateUsing(function ($record) {
                        $mulai   = $record->waktu_unggah_mulai?->format('d-m-Y H:i');
                        $selesai = $record->waktu_unggah_selesai?->format('d-m-Y H:i');
                        return "{$mulai} → {$selesai}";
                    })
                    ->color(fn($record) => match (true) {
                        $record->waktu_unggah_mulai   && now()->lt($record->waktu_unggah_mulai)   => 'gray',
                        $record->waktu_unggah_selesai && now()->gt($record->waktu_unggah_selesai) => 'danger',
                        default                                     => 'success',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenisDokumen_id')
                    ->label('Jenis Dokumen')
                    ->relationship('jenisDokumen', 'nama')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('kirim_notifikasi')
                    ->label('Kirim Notifikasi')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-bell')
                    ->requiresConfirmation()
                    ->modalDescription('Apakah Anda yakin ingin mengirim notifikasi WhatsApp ke semua pengguna terkait jadwal dokumen ini?')
                    ->action(function (JadwalDokumen $record) {
                        $notifikasi = JadwalDokumenService::notifikasiFind($record);
                        foreach ($notifikasi as $notif) {
                            $user  = $notif['user'];
                            $pesan = $notif['pesan'];
                            WhatsAppService::sendMessage($user->nomor_whatsapp, $pesan);
                        }
                        Notification::make()->title('Notifikasi WhatsApp berhasil dikirim')->success()->send();
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalDokumens::route('/'),
            // 'create' => Pages\CreateJadwalDokumen::route('/create'),
            // 'edit' => Pages\EditJadwalDokumen::route('/{record}/edit'),
        ];
    }
}
