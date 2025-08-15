<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisDokumen;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'nama'                 => 'RKA',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            [
                'nama'                 => 'DPA',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            [
                'nama'                 => 'RKAP',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            [
                'nama'                 => 'DPPA',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            [
                'nama'                 => 'LAKIP',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
            ],
            [
                'nama'                 => 'Renja',
                'waktu_unggah_mulai'   => now(),
                'waktu_unggah_selesai' => now()->addDays(10),
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
