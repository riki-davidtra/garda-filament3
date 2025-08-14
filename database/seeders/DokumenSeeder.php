<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Subbagian;

class DokumenSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jenisDokumens = JenisDokumen::all();
        $subbagians    = Subbagian::all();

        foreach ($jenisDokumens as $jenis) {
            foreach ($subbagians as $subbagian) {
                Dokumen::updateOrCreate(
                    [
                        'jenis_dokumen_id' => $jenis->id,
                        'subbagian_id'     => $subbagian->id,
                        'subkegiatan_id'   => 1,
                        'nama'             => 'Nama Dokumen ' . $jenis->nama . ' - ' . $subbagian->nama,
                        'tahun'            => 2024,
                    ],
                    [
                        'keterangan' => 'Dokumen tahun 2024 untuk jenis ' . $jenis->nama . ' di subbagian ' . $subbagian->nama,
                        'status'     => 'Menunggu Persetujuan',
                        'komentar'   => '',
                    ]
                );

                Dokumen::updateOrCreate(
                    [
                        'jenis_dokumen_id' => $jenis->id,
                        'subbagian_id'     => $subbagian->id,
                        'subkegiatan_id'   => 1,
                        'nama'             => 'Nama Dokumen ' . $jenis->nama . ' - ' . $subbagian->nama,
                        'tahun'            => 2025,
                    ],
                    [
                        'keterangan' => 'Dokumen tahun 2025 untuk jenis ' . $jenis->nama . ' di subbagian ' . $subbagian->nama,
                        'status'     => 'Menunggu Persetujuan',
                        'komentar'   => '',
                    ]
                );
            }
        }
    }
}
