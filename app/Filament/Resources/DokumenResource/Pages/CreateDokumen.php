<?php

namespace App\Filament\Resources\DokumenResource\Pages;

use App\Filament\Resources\DokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisDokumen;
use Filament\Actions\Action;

class CreateDokumen extends CreateRecord
{
    protected static string $resource = DokumenResource::class;

    public ?int $jenis_dokumen_id  = null;
    public ?int $jadwal_dokumen_id = null;

    public function mount(): void
    {
        parent::mount();

        // Mengambil parameter jenis_dokumen_id & jadwal_dokumen_id dari query string
        $this->jenis_dokumen_id  = request()->query('jenis_dokumen_id');
        $this->jadwal_dokumen_id = request()->query('jadwal_dokumen_id');

        // Batasi akses route berdasarkan rentang waktu unggah 
        $jenis = JenisDokumen::with('jadwalDokumens')->find($this->jenis_dokumen_id);

        if (!$jenis) {
            abort(404, 'Jenis dokumen tidak ditemukan.');
        }

        $sekarang = now();

        // Jadwal aktif saat ini
        $jadwalAktif = $jenis->jadwalDokumens
            ->first(
                fn($jadwal) =>
                $jadwal->waktu_unggah_mulai &&
                    $jadwal->waktu_unggah_selesai &&
                    $sekarang->between($jadwal->waktu_unggah_mulai, $jadwal->waktu_unggah_selesai)
            );

        // Validasi jadwal_dokumen_id jika dikirim di URL
        if ($this->jadwal_dokumen_id) {
            $jadwalDipilih = $jenis->jadwalDokumens
                ->where('id', $this->jadwal_dokumen_id)
                ->first();

            if (!$jadwalDipilih) {
                abort(403, 'Jadwal dokumen tidak valid.');
            }

            // Pastikan jadwal yang dikirim masih aktif
            if (! $sekarang->between($jadwalDipilih->waktu_unggah_mulai, $jadwalDipilih->waktu_unggah_selesai)) {
                abort(403, 'Jadwal dokumen yang dipilih tidak aktif.');
            }
        } else {
            // Kalau tidak dikirim, gunakan jadwal aktif otomatis (boleh null)
            $this->jadwal_dokumen_id = null;
        }
    }

    public function getBreadcrumbs(): array
    {
        // Custom navigasi breadcrumbs untuk halaman ini
        return [
            ListDokumens::getUrl(['jenis_dokumen_id' => $this->jenis_dokumen_id]) => 'Daftar Dokumen',
            'Unggah',
        ];
    }

    public function getTitle(): string
    {
        $jenis = JenisDokumen::find($this->jenis_dokumen_id);
        return $jenis ? 'Unggah Dokumen ' . $jenis->nama : 'Unggah Dokumen';
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Menyimpan jenis_dokumen_id & subbagian_id yang membuat dokumen
    //     $data['jenis_dokumen_id']  = $data['jenis_dokumen_id'] ?? $this->jenis_dokumen_id;
    //     $data['jadwal_dokumen_id'] = $data['jadwal_dokumen_id'] ?? $this->jadwal_dokumen_id;
    //     $data['subbagian_id']      = Auth::user()?->subbagian_id;

    //     return $data;
    // }

    protected function getCreateFormAction(): Action
    {
        // Ganti label tombol submit
        return parent::getCreateFormAction()->label('Kirim');
    }

    // Hilangkan tombol create another
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        // Menentukan URL redirect setelah dokumen berhasil dibuat.
        return $this->getResource()::getUrl('view', [
            'record'            => $this->record->uuid,
            'jenis_dokumen_id'  => $this->jenis_dokumen_id,
        ]);
    }
}
