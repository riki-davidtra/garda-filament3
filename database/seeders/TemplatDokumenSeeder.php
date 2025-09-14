<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TemplatDokumen;
use App\Models\JenisDokumen;
use App\Models\File;

class TemplatDokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jenisIds = JenisDokumen::all();

        foreach ($jenisIds as $item) {
            $templatDokumen = TemplatDokumen::create([
                'nama'             => "Templat {$item->nama}",
                'jenis_dokumen_id' => $item->id,
            ]);

            File::create([
                'model_type' => TemplatDokumen::class,
                'model_id'   => $templatDokumen->id,
                'tag'        => 'templat_dokumen',
            ]);
        }
    }
}
