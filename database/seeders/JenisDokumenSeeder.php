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
            ['nama' => 'Jenis Dokumen 1',],
            ['nama' => 'Jenis Dokumen 2',],
            ['nama' => 'Jenis Dokumen 3',],
        ];

        foreach ($data as $item) {
            JenisDokumen::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama' => $item['nama'],
                ]
            );
        }
    }
}
