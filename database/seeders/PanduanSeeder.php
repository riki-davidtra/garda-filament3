<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Panduan;

class PanduanSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'judul'     => 'Panduan Unggah Dokumen',
                'deskripsi' => 'Panduan lengkap cara mengunggah dokumen melalui sistem.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Edit Dokumen',
                'deskripsi' => 'Langkah-langkah untuk mengubah atau memperbarui dokumen yang sudah diunggah.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Isi Formulir',
                'deskripsi' => 'Cara mengisi formulir dalam sistem dengan benar dan lengkap.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Edit Formulir',
                'deskripsi' => 'Petunjuk mengubah data atau informasi di formulir yang sudah diisi.',
                'file'      => null,
            ],
        ];

        foreach ($data as $index => $item) {
            Panduan::updateOrCreate(
                ['judul' => $item['judul']],
                [
                    'deskripsi' => $item['deskripsi'],
                    'file'      => $item['file'],
                    'order'     => $index + 1,
                ]
            );
        }
    }
}
