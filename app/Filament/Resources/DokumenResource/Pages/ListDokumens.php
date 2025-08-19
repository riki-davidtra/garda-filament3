<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisDokumen;

class ListDokumens extends ListRecords
{
    protected static string $resource = DokumenResource::class;

    public ?int $jenis_dokumen_id = null;

    public function mount(): void
    {
        parent::mount();

        // Mengambil parameter jenis_dokumen_id dari query string
        $this->jenis_dokumen_id = request()->query('jenis_dokumen_id');
    }

    public function getBreadcrumbs(): array
    {
        // Custom navigasi breadcrumbs untuk halaman ini
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Daftar',
        ];
    }

    public function getTitle(): string
    {
        $jenis = JenisDokumen::find($this->jenis_dokumen_id);
        return $jenis ? 'Daftar Dokumen ' . $jenis->nama : 'Daftar Dokumen';
    }

    protected function getHeaderActions(): array
    {
        // hanya tampilkan tombol kalau bisa upload atau admin
        if (! $this->jenis_dokumen_id) {
            return [];
        }

        $jenis = \App\Models\JenisDokumen::find($this->jenis_dokumen_id);

        if (! $jenis) {
            return [];
        }

        $now        = now();
        $mulai      = $jenis->waktu_unggah_mulai ? \Carbon\Carbon::parse($jenis->waktu_unggah_mulai) : null;
        $selesai    = $jenis->waktu_unggah_selesai ? \Carbon\Carbon::parse($jenis->waktu_unggah_selesai) : null;
        $bisaUnggah = ($mulai && $selesai) ? $now->between($mulai, $selesai) : false;

        $user    = Auth::user();
        $isAdmin = $user->hasRole('Super Admin') || $user->hasRole('admin');

        if (!$bisaUnggah && !$isAdmin) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Unggah Dokumen')
                ->url(fn() => DokumenResource::getUrl('create', [
                    'jenis_dokumen_id' => $this->jenis_dokumen_id,
                ])),
        ];
    }

    protected function getQueryString(): array
    {
        return [
            // Menyimpan jenis_dokumen_id di query string kecuali jika null & menyertakan query string default dari parent
            'jenis_dokumen_id' => [
                'except' => null,
            ],
            ...parent::getQueryString(),
        ];
    }
}
