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
                'nama'                 => 'RKA',
                'waktu_unggah_mulai'   => now()->subDays(2), // Mulai 2 hari lalu
                'waktu_unggah_selesai' => now()->addDays(5), // Selesai 5 hari lagi
                'batas_unggah'         => 2,
            ],
            // Belum mulai
            [
                'nama'                 => 'DPA',
                'waktu_unggah_mulai'   => now()->addDays(3), // Mulai 3 hari lagi
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            // Sudah berakhir
            [
                'nama'                 => 'RKAP',
                'waktu_unggah_mulai'   => now()->subDays(10),
                'waktu_unggah_selesai' => now()->subDays(2), // Selesai 2 hari lalu
                'batas_unggah'         => 2,
            ],
            // Sedang berlangsung
            [
                'nama'                 => 'DPPA',
                'waktu_unggah_mulai'   => now()->subDay(),
                'waktu_unggah_selesai' => now()->addDays(7),
                'batas_unggah'         => 2,
            ],
            // Belum mulai
            [
                'nama'                 => 'LAKIP',
                'waktu_unggah_mulai'   => now()->addDays(1),
                'waktu_unggah_selesai' => now()->addDays(8),
                'batas_unggah'         => 2,
            ],
            // Sudah berakhir
            [
                'nama'                 => 'Renja',
                'waktu_unggah_mulai'   => now()->subDays(8),
                'waktu_unggah_selesai' => now()->subDays(1),
                'batas_unggah'         => 2,
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
