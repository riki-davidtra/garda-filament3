<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Resources\Pages\ViewRecord;

use Filament\Infolists;
use Filament\Forms;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\JenisDokumen;
use App\Models\Dokumen;
use App\Services\DokumenService;
use App\Services\WhatsAppService;

class ViewDokumen extends ViewRecord
{
    protected static string $resource = DokumenResource::class;
    protected static ?string $title   = 'Detail Dokumen';

    public ?int $jenis_dokumen_id      = null;
    public ?int $jadwal_dokumen_id     = null;
    public ?JenisDokumen $jenisDokumen = null;

    public function mount(string|int $record): void
    {
        parent::mount($record);

        $this->jenis_dokumen_id  = request()->query('jenis_dokumen_id');
        $this->jadwal_dokumen_id = request()->query('jadwal_dokumen_id');
        $this->jenisDokumen      = JenisDokumen::find($this->jenis_dokumen_id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Detail',
        ];
    }

    public function getTitle(): string
    {
        return $this->jenisDokumen ? 'Detail Dokumen ' . $this->jenisDokumen->nama : 'Detail Dokumen';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');

        return [
            Actions\Action::make('kirim_notifikasi')
                ->label('Kirim Notifikasi')
                ->color('success')
                ->icon('heroicon-o-bell')
                ->requiresConfirmation()
                ->modalDescription('Apakah Anda yakin ingin mengirim notifikasi WhatsApp kepada semua pengguna terkait status dokumen ini?')
                ->action(function ($record) {
                    if (!($record instanceof Dokumen)) {
                        return;
                    }

                    $notifikasi = DokumenService::notifikasiFind($record);

                    foreach ($notifikasi as $notif) {
                        $user  = $notif['user'];
                        $pesan = $notif['pesan'];
                        if ($user?->nomor_whatsapp) {
                            WhatsAppService::sendMessage($user->nomor_whatsapp, $pesan);
                        }
                    }

                    Notification::make()
                        ->title('Notifikasi WhatsApp berhasil dikirim')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $isSuperOrAdmin || $isPerencana),

            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(fn($record) => route('filament.admin.resources.dokumens.edit', [
                    'record'           => $record->uuid,
                    'jenis_dokumen_id' => $this->jenis_dokumen_id,
                ]))
                ->visible(fn($record) => $this->canEditRecord($record, $user, $isSuperOrAdmin)),
        ];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        $user          = Auth::user();
        $canEditStatus = $user->hasAnyRole(['Super Admin', 'admin', 'perencana']);

        return $infolist->schema([
            Infolists\Components\Tabs::make('Tab')
                ->tabs([
                    Infolists\Components\Tabs\Tab::make('Utama')
                        ->schema([
                            Infolists\Components\TextEntry::make('nama')
                                ->label('Nama'),
                            Infolists\Components\TextEntry::make('tahun')
                                ->label('Tahun'),
                            Infolists\Components\TextEntry::make('periode')
                                ->label('Periode')
                                ->visible(fn() => $this->jenisDokumen?->mode_periode),
                            Infolists\Components\TextEntry::make('subkegiatan.nama')
                                ->label('Subkegiatan')
                                ->visible(fn() => $this->jenisDokumen?->mode_subkegiatan),
                            Infolists\Components\TextEntry::make('keterangan')
                                ->label('Keterangan'),

                            Infolists\Components\Section::make('Status Dokumen')
                                ->schema([
                                    Infolists\Components\TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'Menunggu Persetujuan'        => 'warning',
                                            'Diterima'                    => 'success',
                                            'Ditolak'                     => 'danger',
                                            'Revisi Menunggu Persetujuan' => 'warning',
                                            'Revisi Diterima'             => 'success',
                                            'Revisi Ditolak'              => 'danger',
                                            default                       => 'gray',
                                        }),

                                    Infolists\Components\TextEntry::make('komentar')
                                        ->label('Komentar')
                                        ->placeholder('-')
                                        ->columnSpanFull(),

                                    Infolists\Components\Actions::make([
                                        Infolists\Components\Actions\Action::make('ubahStatus')
                                            ->label('Ubah Status & Komentar')
                                            ->icon('heroicon-m-pencil')
                                            ->color('warning')
                                            ->modalHeading('Ubah Status & Komentar')
                                            ->modalWidth('sm')
                                            ->form([
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
                                                    ->default(fn($record) => $record->status)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        $set('komentar', null);
                                                    }),

                                                Forms\Components\Textarea::make('komentar')
                                                    ->label('Komentar')
                                                    ->rows(3)
                                                    ->default(fn($record) => $record->komentar),
                                            ])
                                            ->action(fn(array $data, $record) => $this->updateStatus($record, $data))
                                            ->visible($canEditStatus),
                                    ]),
                                ])
                                ->collapsible()
                                ->columns(2)
                                ->visible(fn() => $this->jenisDokumen?->mode_status),
                        ]),

                    Infolists\Components\Tabs\Tab::make('Riwayat Aktivitas')
                        ->schema([
                            Infolists\Components\TextEntry::make('pembuat.name')
                                ->label('Dibuat Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pembuat, $record->dibuat_pada)),

                            Infolists\Components\TextEntry::make('pembaru.name')
                                ->label('Diperbarui Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pembaru, $record->diperbarui_pada)),

                            Infolists\Components\TextEntry::make('penghapus.name')
                                ->label('Dihapus Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->penghapus, $record->dihapus_pada)),

                            Infolists\Components\TextEntry::make('pemulih.name')
                                ->label('Dipulihkan Oleh')
                                ->placeholder('-')
                                ->state(fn($record) => $this->formatUserInfo($record->pemulih, $record->dipulihkan_pada)),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    protected function canEditRecord($record, $user, $isSuperOrAdmin): bool
    {
        if ($isSuperOrAdmin) return true;

        $aksesPeran = $user->roles->pluck('id')
            ->intersect($record->jenisDokumen->roles->pluck('id'));
        return $aksesPeran->isNotEmpty();
    }

    protected function formatUserInfo($user, $tanggal): ?string
    {
        if (!$user && !$tanggal) return null;

        $bagian    = $user?->subbagian?->bagian?->nama;
        $subbagian = $user?->subbagian?->nama;

        $parts = [
            $user?->name,
            $user?->nip ? 'NIP: ' . $user->nip                            : null,
            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
        ];

        return implode(' | ', array_filter($parts));
    }

    protected function updateStatus($record, array $data): void
    {
        $record->update([
            'status'   => $data['status'],
            'komentar' => $data['komentar'],
        ]);

        $record->refresh();

        Notification::make()
            ->title('Berhasil')
            ->body('Status & komentar berhasil diperbarui.')
            ->success()
            ->send();
    }
}
