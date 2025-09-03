<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisDokumen;
use App\Models\JadwalDokumen;
use Carbon\Carbon;

class JadwalDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'                 => 'Pengarahan',
                'waktu_unggah_mulai'   => Carbon::now()->addDays(3),    // 3 hari sebelum mulai
                'waktu_unggah_selesai' => Carbon::now()->addDays(10),
            ],
            [
                'nama'                 => 'DPA Murni',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(2),   // sedang berlangsung
                'waktu_unggah_selesai' => Carbon::now()->addDays(5),
            ],
            [
                'nama'                 => 'DPA Perubahan',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(2),
                'waktu_unggah_selesai' => Carbon::now()->addDays(3),   // 3 hari sebelum berakhir
            ],
            [
                'nama'                 => 'DPA Pergeseran',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(10),
                'waktu_unggah_selesai' => Carbon::now()->subDays(2),    // sudah berakhir
            ],
            [
                'nama'                 => 'RKA Murni',
                'waktu_unggah_mulai'   => Carbon::now()->addDays(3),
                'waktu_unggah_selesai' => Carbon::now()->addDays(10),   // 3 hari sebelum mulai
            ],
            [
                'nama'                 => 'RKA Perubahan',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(2),
                'waktu_unggah_selesai' => Carbon::now()->addDays(5),   // sedang berlangsung
            ],
            [
                'nama'                 => 'KAK',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(2),
                'waktu_unggah_selesai' => Carbon::now()->addDays(3),   // 3 hari sebelum berakhir
            ],
            [
                'nama'                 => 'Laporan Realisasi',
                'waktu_unggah_mulai'   => Carbon::now()->subDays(10),
                'waktu_unggah_selesai' => Carbon::now()->subDays(2),    // sudah berakhir
            ],
        ];

        foreach ($data as $item) {
            $jenis = JenisDokumen::firstOrCreate(['nama' => $item['nama']]);

            JadwalDokumen::updateOrCreate(
                ['jenis_dokumen_id' => $jenis->id],
                [
                    'kode'                 => 'JD' . mt_rand(100000, 999999),
                    'waktu_unggah_mulai'   => $item['waktu_unggah_mulai'],
                    'waktu_unggah_selesai' => $item['waktu_unggah_selesai'],
                    'aktif'                => true,
                ]
            );
        }
    }
}
