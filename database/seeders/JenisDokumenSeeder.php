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

        $formatFiles = FormatFile::whereIn('ekstensi', ['pdf', 'doc', 'docx', 'xls', 'xlsx'])->pluck('id')->toArray();

        $data = [
            [
                'nama'                 => 'Dokumen Pengarahan',
                'waktu_unggah_mulai'   => now()->subDays(2),
                'waktu_unggah_selesai' => now()->addDays(5),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'DPA Murni',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'DPA Perubahan',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'DPA Pergeseran',
                'waktu_unggah_mulai'   => now()->addDays(3),
                'waktu_unggah_selesai' => now()->addDays(10),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'RKA Murni',
                'waktu_unggah_mulai'   => now()->subDays(10),
                'waktu_unggah_selesai' => now()->subDays(2),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'RKA Perubahan',
                'waktu_unggah_mulai'   => now()->subDay(),
                'waktu_unggah_selesai' => now()->addDays(7),
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'KAK',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
            [
                'nama'                 => 'Laporan Realisasi',
                'waktu_unggah_mulai'   => null,
                'waktu_unggah_selesai' => null,
                'batas_unggah'         => 2,
                'format_file'          => $formatFiles,
                'maksimal_ukuran'      => 20480,
            ],
        ];

        foreach ($data as $item) {
            $dokumen = JenisDokumen::updateOrCreate(['nama' => $item['nama']], $item);

            if (in_array($dokumen->nama, ['Dokumen Pengarahan', 'DPA Murni', 'DPA Perubahan', 'DPA Pergeseran'])) {
                $dokumen->roles()->sync([$superAdmin->id, $admin->id, $pimpinan->id, $perencana->id]);
            } else {
                $dokumen->roles()->sync([$superAdmin->id, $admin->id, $pimpinan->id, $perencana->id, $userSubbagian->id]);
            }
        }
    }
}
