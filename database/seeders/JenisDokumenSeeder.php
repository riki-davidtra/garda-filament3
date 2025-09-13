<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisDokumen;
use Spatie\Permission\Models\Role;
use App\Models\FormatFile;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $perencana     = Role::firstOrCreate(['name' => 'perencana']);
        $userSubbagian = Role::firstOrCreate(['name' => 'subbagian']);

        $formatFiles = FormatFile::whereIn('ekstensi', ['pdf', 'doc', 'docx', 'xls', 'xlsx'])
            ->pluck('id')
            ->toArray();

        $dataModeFalse = [
            'Pengarahan',
            'DPA Murni',
            'DPA Perubahan',
            'DPA Pergeseran',
        ];

        $dataAll = [
            'Pengarahan',
            'DPA Murni',
            'DPA Perubahan',
            'DPA Pergeseran',
            'RKA Murni',
            'RKA Perubahan',
            'RKA Pergeseran',
            'KAK',
            'Laporan Realisasi',
        ];

        foreach ($dataAll as $nama) {
            $isModeStatusTrue  = !in_array($nama, $dataModeFalse);
            $isModeSubkegiatan = $nama !== 'Pengarahan';

            $dokumen = JenisDokumen::updateOrCreate(
                ['nama' => $nama],
                [
                    'batas_unggah'     => 10,
                    'format_file'      => $formatFiles,
                    'maksimal_ukuran'  => 20480,
                    'mode_status'      => $isModeStatusTrue,
                    'mode_subkegiatan' => $isModeSubkegiatan,
                    'mode_periode'     => $nama === 'RKA Pergeseran',
                ]
            );

            // Atur role
            if ($isModeStatusTrue) {
                $dokumen->roles()->sync([$userSubbagian->id]);
            } else {
                $dokumen->roles()->sync([$perencana->id]);
            }
        }
    }
}
