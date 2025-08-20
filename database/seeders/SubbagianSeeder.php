<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subbagian;
use App\Models\Bagian;

class SubbagianSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $bagians = Bagian::all();

        $data = [
            ['nama' => 'Subbagian Tata Usaha'],
            ['nama' => 'Subbagian Kepegawaian'],
            ['nama' => 'Subbagian Akuntansi'],
        ];

        foreach ($bagians as $bagian) {
            foreach ($data as $item) {
                Subbagian::updateOrCreate(
                    [
                        'bagian_id' => $bagian->id,
                        'nama'      => $item['nama'],
                    ],
                    [
                        'bagian_id' => $bagian->id,
                        'nama'      => $item['nama'],
                    ]
                );
            }
        }
    }
}
