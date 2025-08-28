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
        $jenisIds = JenisDokumen::all();

        foreach ($jenisIds as $item) {
            TemplatDokumen::create([
                'nama'             => "Templat {$item->nama}",
                'jenis_dokumen_id' => $item->id,
                'path'             => null,
            ]);
        }
    }
}
