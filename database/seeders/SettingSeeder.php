<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'konfigurasiSitus' => Setting::updateOrCreate(['name' => 'Konfigurasi Situs']),
        ];

        $settingItems = [
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Nama Situs',
                'key'        => 'site_name',
                'type'       => 'text',
                'value'      => 'GARDA - BIRO UMUM',
            ],
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Nama Panjang Situs',
                'key'        => 'site_full_name',
                'type'       => 'text',
                'value'      => 'Gerbang Arsip Rencana dan Data Administratif',
            ],
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Logo',
                'key'        => 'logo',
                'type'       => 'file',
                'value'      => null,
            ],
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Favicon',
                'key'        => 'favicon',
                'type'       => 'file',
                'value'      => null,
            ],
        ];
        foreach ($settingItems as $settingItem) {
            SettingItem::updateOrCreate(['name' => $settingItem['name']], $settingItem);
        }
    }
}
