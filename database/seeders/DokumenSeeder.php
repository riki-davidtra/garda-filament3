<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\JadwalDokumen;
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
            $jadwal = JadwalDokumen::where('jenis_dokumen_id', $jenis->id)->first();

            foreach ($subbagians as $subbagian) {
                Dokumen::updateOrCreate(
                    [
                        'jenis_dokumen_id'  => $jenis->id,
                        'jadwal_dokumen_id' => $jadwal?->id,
                        'subbagian_id'      => $subbagian->id,
                        'subkegiatan_id'    => 1,
                        'nama'              => 'Nama Dokumen ' . $jenis->nama,
                        'tahun'             => 2025,
                    ],
                    [
                        'periode'    => $jenis->mode_periode ? rand(1, 5) : null,
                        'keterangan' => 'Dokumen tahun 2025 untuk jenis ' . $jenis->nama . ' di subbagian ' . $subbagian->nama,
                        'status'     => 'Menunggu Persetujuan',
                        'komentar'   => '',
                    ]
                );
            }
        }
    }
}
