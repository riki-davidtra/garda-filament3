<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisDokumen;

class CreateDokumen extends CreateRecord
{
    protected static string $resource = DokumenResource::class;

    public ?int $jenis_dokumen_id = null;

    public function mount(): void
    {
        parent::mount();

        // Mengambil parameter jenis_dokumen_id dari query string
        $this->jenis_dokumen_id = request()->query('jenis_dokumen_id');

        // Batasi akses route berdasarkan rentang waktu unggah 
        $jenis = JenisDokumen::find($this->jenis_dokumen_id);

        if (!$jenis) {
            abort(404, 'Jenis dokumen tidak ditemukan.');
        }

        $sekarang = now();

        if ($sekarang->lt($jenis->waktu_unggah_mulai)) {
            abort(403, 'Belum masuk waktu unggah untuk dokumen ini.');
        }

        if ($sekarang->gt($jenis->waktu_unggah_selesai)) {
            abort(403, 'Waktu unggah dokumen ini sudah berakhir.');
        }
    }

    public function getBreadcrumbs(): array
    {
        // Custom navigasi breadcrumbs untuk halaman ini
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Buat',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Menyimpan jenis_dokumen_id & subbagian_id yang membuat dokumen
        if (empty($data['jenis_dokumen_id'])) {
            $data['jenis_dokumen_id'] = $this->jenis_dokumen_id;
        }
        $data['subbagian_id'] = auth()->check() ? auth()->user()->subbagian_id : null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Menentukan URL redirect setelah dokumen berhasil dibuat.
        return $this->getResource()::getUrl('edit', [
            'record'           => $this->record->uuid,
            'jenis_dokumen_id' => $this->jenis_dokumen_id,
        ]);
    }
}
