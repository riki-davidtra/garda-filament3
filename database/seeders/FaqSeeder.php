<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'pertanyaan' => 'Bagaimana cara mengunggah dokumen?',
                'jawaban'    => 'Masuk ke menu Manajemen Dokumen, pilih kategori, lalu unggah file sesuai format yang ditentukan.'
            ],
            [
                'pertanyaan' => 'Format file apa saja yang didukung?',
                'jawaban'    => 'PDF, DOCX, XLSX, JPG, dan PNG.'
            ],
            [
                'pertanyaan' => 'Bagaimana jika saya lupa password?',
                'jawaban'    => 'Gunakan fitur Lupa Password pada halaman login untuk mengatur ulang kata sandi Anda.'
            ],
            [
                'pertanyaan' => 'Apakah dokumen yang diunggah aman?',
                'jawaban'    => 'Ya, dokumen dienkripsi dan hanya dapat diakses oleh pengguna yang memiliki hak akses.'
            ],
            [
                'pertanyaan' => 'Bagaimana cara melihat versi sebelumnya dari dokumen?',
                'jawaban'    => 'Buka detail dokumen dan pilih menu Riwayat Versi untuk memulihkan atau melihat versi sebelumnya.'
            ],
        ];

        foreach ($data as $index => $item) {
            Faq::updateOrCreate(
                ['pertanyaan' => $item['pertanyaan']],
                [
                    'pertanyaan' => $item['pertanyaan'],
                    'jawaban'    => $item['jawaban'],
                    'order'      => $index + 1,
                ]
            );
        }
    }
}
