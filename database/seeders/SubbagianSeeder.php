<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subbagian;

class SubbagianSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Subbagian 1',],
            ['nama' => 'Subbagian 2',],
            ['nama' => 'Subbagian 3',],
        ];

        foreach ($data as $item) {
            Subbagian::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama' => $item['nama'],
                ]
            );
        }
    }
}
