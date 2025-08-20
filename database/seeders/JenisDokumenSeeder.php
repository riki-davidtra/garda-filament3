<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisDokumen;
use Carbon\Carbon;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            // Sedang berlangsung
            [
                'nama'                 => 'Surat Edaran',
                'waktu_unggah_mulai'   => now()->subDays(2),   // Mulai 2 hari lalu
                'waktu_unggah_selesai' => now()->addDays(5),   // Selesai 5 hari lagi
                'batas_unggah'         => 2,
                'subbagian_id'         => 3,
            ],
            // Belum mulai
            [
                'nama'                 => 'DPPA',
                'waktu_unggah_mulai'   => now()->addDays(3),    // Mulai 3 hari lagi
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
                'subbagian_id'         => 3,
            ],
            // Sudah berakhir
            [
                'nama'                 => 'RKA Murni',
                'waktu_unggah_mulai'   => now()->subDays(10),
                'waktu_unggah_selesai' => now()->subDays(2),    // Selesai 2 hari lalu
                'batas_unggah'         => 2,
                'subbagian_id'         => 4,
            ],
            // Sedang berlangsung
            [
                'nama'                 => 'RKA Perubahan',
                'waktu_unggah_mulai'   => now()->subDay(),
                'waktu_unggah_selesai' => now()->addDays(7),
                'batas_unggah'         => 2,
                'subbagian_id'         => 4,
            ],
            // null
            [
                'nama'                 => 'KAK',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
                'batas_unggah'         => 2,
                'subbagian_id'         => 4,
            ],
            // Sudah berakhir
            [
                'nama'                 => 'Laporan Realisasi',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
                'batas_unggah'         => 2,
                'subbagian_id'         => 4,
            ],
        ];

        foreach ($data as $item) {
            JenisDokumen::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
