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
            ['nama' => 'PDF', 'ekstensi' => 'pdf', 'mime_types' => 'application/pdf'],

            ['nama' => 'Word DOC', 'ekstensi' => 'doc', 'mime_types' => 'application/msword'],
            ['nama' => 'Word DOCX', 'ekstensi' => 'docx', 'mime_types' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],

            ['nama' => 'Excel XLS', 'ekstensi' => 'xls', 'mime_types' => 'application/vnd.ms-excel'],
            ['nama' => 'Excel XLSX', 'ekstensi' => 'xlsx', 'mime_types' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],

            ['nama' => 'PowerPoint PPT', 'ekstensi' => 'ppt', 'mime_types' => 'application/vnd.ms-powerpoint'],
            ['nama' => 'PowerPoint PPTX', 'ekstensi' => 'pptx', 'mime_types' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],

            ['nama' => 'JPG', 'ekstensi' => 'jpg', 'mime_types' => 'image/jpg'],
            ['nama' => 'JPEG', 'ekstensi' => 'jpeg', 'mime_types' => 'image/jpeg'],
            ['nama' => 'PNG', 'ekstensi' => 'png', 'mime_types' => 'image/png'],
            ['nama' => 'HEIC', 'ekstensi' => 'heic', 'mime_types' => 'image/heic'],
            ['nama' => 'HEIF', 'ekstensi' => 'heif', 'mime_types' => 'image/heif'],
        ];

        foreach ($data as $item) {
            FormatFile::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama'       => $item['nama'],
                    'ekstensi'   => $item['ekstensi'],
                    'mime_types' => $item['mime_types'],
                ]
            );
        }
    }
}
