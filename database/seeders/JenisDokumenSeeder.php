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
            ['nama' => 'RKA'],
            ['nama' => 'DPA'],
            ['nama' => 'RKAP'],
            ['nama' => 'DPPA'],
            ['nama' => 'LAKIP'],
            ['nama' => 'Renja'],
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
