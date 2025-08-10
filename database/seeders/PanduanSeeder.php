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
                'judul'     => 'Panduan Upload Dokumen',
                'deskripsi' => 'Panduan lengkap cara mengunggah dokumen melalui sistem.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Format File',
                'deskripsi' => 'Penjelasan format file yang didukung sistem.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Reset Password',
                'deskripsi' => 'Langkah-langkah mengatur ulang kata sandi akun Anda.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Keamanan Dokumen',
                'deskripsi' => 'Tips dan prosedur menjaga keamanan dokumen di sistem.',
                'file'      => null,
            ],
            [
                'judul'     => 'Panduan Riwayat Versi',
                'deskripsi' => 'Cara melihat dan memulihkan versi sebelumnya dari dokumen.',
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
