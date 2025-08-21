<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indikator;

class IndikatorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Jumlah pelayanan kedinasan KDH dan WKDH'],
            ['nama' => 'Jumlah dokumen pelaporan keuangan dan penatausahaan aset'],
            ['nama' => 'Jumlah pelayanan jamuan/audiensi dan penerimaan tamu pemda'],
            ['nama' => 'Jumlah naskah dinas yang ditindaklanjuti oleh pimpinan'],
            ['nama' => 'Jumlah pelayanan penggunaan kendaraan dinas/operasional'],
            ['nama' => 'Jumlah pelayanan penggunaan ruang rapat/aula'],
        ];

        foreach ($data as $item) {
            Indikator::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'nama' => $item['nama'],
                ]
            );
        }
    }
}
