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
        $superAdmin    = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin         = Role::firstOrCreate(['name' => 'admin']);
        $pimpinan      = Role::firstOrCreate(['name' => 'pimpinan']);
        $perencana     = Role::firstOrCreate(['name' => 'perencana']);
        $userSubbagian = Role::firstOrCreate(['name' => 'subbagian']);

        $formatFiles = FormatFile::whereIn('ekstensi', ['pdf', 'doc', 'docx', 'xls', 'xlsx'])
            ->pluck('id')
            ->toArray();

        $data = [
            'Dokumen Pengarahan',
            'DPA Murni',
            'DPA Perubahan',
            'DPA Pergeseran',
            'RKA Murni',
            'RKA Perubahan',
            'KAK',
            'Laporan Realisasi',
        ];

        foreach ($data as $nama) {
            $dokumen = JenisDokumen::updateOrCreate(
                ['nama' => $nama],
                [
                    'batas_unggah'    => 10,
                    'format_file'     => $formatFiles,
                    'maksimal_ukuran' => 20480,
                ]
            );

            // Atur role
            if (in_array($dokumen->nama, ['Dokumen Pengarahan', 'DPA Murni', 'DPA Perubahan', 'DPA Pergeseran'])) {
                $dokumen->roles()->sync([$perencana->id]);
            } else {
                $dokumen->roles()->sync([$userSubbagian->id]);
            }
        }
    }
}
