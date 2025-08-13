<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subkegiatan;

class SubkegiatanSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Subkegiatan Pembangunan Infrastruktur'],
            ['nama' => 'Subkegiatan Pendidikan dan Pelatihan'],
            ['nama' => 'Subkegiatan Kesehatan Masyarakat'],
            ['nama' => 'Subkegiatan Pengembangan Ekonomi'],
            ['nama' => 'Subkegiatan Lingkungan Hidup'],
            ['nama' => 'Subkegiatan Sosial Budaya'],
            ['nama' => 'Subkegiatan Keamanan dan Ketertiban'],
        ];

        foreach ($data as $item) {
            Subkegiatan::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama' => $item['nama'],
                ]
            );
        }
    }
}
