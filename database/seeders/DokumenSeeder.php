<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokumen;
use App\Models\JenisDokumen;

class DokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jenisDokumens = JenisDokumen::all();

        foreach ($jenisDokumens as $jenis) {
            Dokumen::updateOrCreate(
                [
                    'jenis_dokumen_id' => $jenis->id,
                    'nama'             => 'Nama Dokumen ' . $jenis->nama,
                    'tahun'            => 2024,
                    'subkegiatan_id'   => 1,
                ],
                [
                    'waktu_unggah_mulai'   => now(),
                    'waktu_unggah_selesai' => now()->addDays(10),
                    'keterangan'           => 'Dokumen tahun 2024 untuk jenis ' . $jenis->nama,
                ]
            );
        }

        foreach ($jenisDokumens as $jenis) {
            Dokumen::updateOrCreate(
                [
                    'jenis_dokumen_id' => $jenis->id,
                    'nama'             => 'Nama Dokumen ' . $jenis->nama,
                    'tahun'            => 2025,
                    'subkegiatan_id'   => 1,
                ],
                [
                    'waktu_unggah_mulai'   => now(),
                    'waktu_unggah_selesai' => now()->addDays(10),
                    'keterangan'           => 'Dokumen tahun 2025 untuk jenis ' . $jenis->nama,
                    'created_by'           => 1,
                ]
            );
        }
    }
}
