<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TemplatDokumen;
use App\Models\FileTemplatDokumen;
use App\Models\JenisDokumen;

class TemplatDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $jenisIds = JenisDokumen::pluck('id')->toArray();

        for ($i = 1; $i <= 5; $i++) {
            $templat = TemplatDokumen::create([
                'jenis_dokumen_id' => $jenisIds ? $jenisIds[array_rand($jenisIds)] : null,
            ]);

            $fileCount = rand(1, 3);
            for ($j = 1; $j <= $fileCount; $j++) {
                FileTemplatDokumen::create([
                    'templat_dokumen_id' => $templat->id,
                    'nama'               => "File Templat {$j}",
                    'path'               => null,
                ]);
            }
        }
    }
}
