<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormatFile;

class FormatFileSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'PDF', 'ekstensi' => 'pdf', 'mime_type' => 'application/pdf'],

            ['nama' => 'Word DOC', 'ekstensi' => 'doc', 'mime_type' => 'application/msword'],
            ['nama' => 'Word DOCX', 'ekstensi' => 'docx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],

            ['nama' => 'Excel XLS', 'ekstensi' => 'xls', 'mime_type' => 'application/vnd.ms-excel'],
            ['nama' => 'Excel XLSX', 'ekstensi' => 'xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],

            ['nama' => 'PowerPoint PPT', 'ekstensi' => 'ppt', 'mime_type' => 'application/vnd.ms-powerpoint'],
            ['nama' => 'PowerPoint PPTX', 'ekstensi' => 'pptx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],

            ['nama' => 'JPG', 'ekstensi' => 'jpg', 'mime_type' => 'image/jpg'],
            ['nama' => 'JPEG', 'ekstensi' => 'jpeg', 'mime_type' => 'image/jpeg'],
            ['nama' => 'PNG', 'ekstensi' => 'png', 'mime_type' => 'image/png'],
            ['nama' => 'HEIC', 'ekstensi' => 'heic', 'mime_type' => 'image/heic'],
            ['nama' => 'HEIF', 'ekstensi' => 'heif', 'mime_type' => 'image/heif'],
        ];

        foreach ($data as $item) {
            FormatFile::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama'       => $item['nama'],
                    'ekstensi'   => $item['ekstensi'],
                    'mime_types' => $item['mime_type'],
                ]
            );
        }
    }
}
