<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bagian;

class BagianSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Bagian Umum'],
            ['nama' => 'Bagian Keuangan'],
            ['nama' => 'Bagian Perencanaan'],
        ];

        foreach ($data as $item) {
            Bagian::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama' => $item['nama'],
                ]
            );
        }
    }
}
