<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bagian;
use App\Models\Subbagian;

class BagianSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            'Rumah Tangga' => [
                'Rumah Tangga Gubernur',
                'Rumah Tangga Wakil Gubernur',
                'Urusan Dalam',
            ],
            'Administrasi Keuangan dan Aset' => [
                'Keuangan dan Verifikasi Setda',
                'Akuntansi dan Penatausahaan Aset',
                'Penggunaan, Pengamanan, dan Pemeliharaan Aset Setda',
            ],
            'Tata Usaha' => [
                'Tata Usaha Pimpinan dan Staf Ahli',
                'Pengelolaan Kendaraan',
                'Persuratan dan Arsip',
            ],
        ];

        foreach ($data as $bagianNama => $subbagians) {
            $bagian = Bagian::updateOrCreate(
                ['nama' => $bagianNama],
                ['nama' => $bagianNama]
            );

            foreach ($subbagians as $subNama) {
                Subbagian::updateOrCreate(
                    ['nama' => $subNama, 'bagian_id' => $bagian->id],
                    ['nama' => $subNama, 'bagian_id' => $bagian->id]
                );
            }
        }
    }
}
