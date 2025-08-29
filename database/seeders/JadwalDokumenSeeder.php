<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisDokumen;
use App\Models\JadwalDokumen;

class JadwalDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'                 => 'Dokumen Pengarahan',
                'waktu_unggah_mulai'   => now()->subDays(2),
                'waktu_unggah_selesai' => now()->addDays(5),
            ],
            [
                'nama'                 => 'DPA Murni',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
            ],
            [
                'nama'                 => 'DPA Perubahan',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
            ],
            [
                'nama'                 => 'DPA Pergeseran',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
            ],
            [
                'nama'                 => 'RKA Murni',
                'waktu_unggah_mulai'   => now()->subDays(10),
                'waktu_unggah_selesai' => now()->subDays(2),
            ],
            [
                'nama'                 => 'RKA Perubahan',
                'waktu_unggah_mulai'   => now()->subDay(),
                'waktu_unggah_selesai' => now()->addDays(7),
            ],
            [
                'nama'                 => 'KAK',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
            ],
            [
                'nama'                 => 'Laporan Realisasi',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
            ],
        ];

        foreach ($data as $item) {
            $jenis = JenisDokumen::where('nama', $item['nama'])->first();

            if ($jenis) {
                JadwalDokumen::updateOrCreate(
                    ['jenis_dokumen_id' => $jenis->id],
                    [
                        'kode'                 => 'JD' . mt_rand(100000, 999999),
                        'waktu_unggah_mulai'   => $item['waktu_unggah_mulai'],
                        'waktu_unggah_selesai' => $item['waktu_unggah_selesai'],
                        'aktif'                => true,
                    ]
                );
            }
        }
    }
}
