<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokumen;

class DokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id'           => 1,
                'tahun'             => now()->year,
                'subbagian_id'      => 1,
                'jenis_dokumen_id'  => 1,
                'tenggat_waktu'     => now()->addDays(10),
                'keterangan'        => 'Dokumen uji coba pertama.',
            ],
            [
                'user_id'          => 1,
                'tahun'            => now()->year,
                'subbagian_id'     => 2,
                'jenis_dokumen_id' => 2,
                'tenggat_waktu'    => now()->addDays(5),
                'keterangan'       => 'Dokumen uji coba kedua.',
            ],
        ];

        foreach ($data as $item) {
            Dokumen::updateOrCreate(
                ['subbagian_id' => $item['subbagian_id']],
                $item
            );
        }
    }
}
