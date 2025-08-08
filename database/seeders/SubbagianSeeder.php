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
            ['nama' => 'Subbagian Tata Pemerintahan'],
            ['nama' => 'Subbagian Otonomi Daerah'],
            ['nama' => 'Subbagian Pemerintahan Umum'],
            ['nama' => 'Subbagian Kerjasama Pemerintah Daerah'],
            ['nama' => 'Subbagian Pertanahan dan Kewilayahan'],
            ['nama' => 'Subbagian Administrasi Kewilayahan'],
            ['nama' => 'Subbagian Kelembagaan dan Analisis Jabatan'],
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
