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
            'kontak'    => Setting::updateOrCreate(['name' => 'Kontak']),
        ];

        $settingItems = [
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Nama Situs',
                'key'        => 'nama_situs',
                'type'       => 'text',
                'value'      => 'GARDA',
            ],
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'URL Situs Web',
                'key'        => 'url_situs_web',
                'type'       => 'url',
                'value'      => 'http://127.0.0.1:8000/',
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
            [
                'setting_id' => $settings['konfigurasiSitus']->id,
                'name'       => 'Meta',
                'key'        => 'meta',
                'type'       => 'textarea',
                'value'      => '<meta name="description" content="" />
    <meta property = "og:title" content       = "SITE NAME" />
    <meta property = "og:description" content = "" />
    <meta property = "og:type" content        = "website" />
    <meta property = "og:url" content         = "https://example.com" />
    <meta property = "og:image" content       = "" />',
            ],
            [
                'setting_id' => $settings['kontak']->id,
                'name'       => 'Alamat',
                'key'        => 'alamat',
                'type'       => 'text',
                'value'      => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum, voluptas!',
            ],
            [
                'setting_id' => $settings['kontak']->id,
                'name'       => 'Email',
                'key'        => 'email',
                'type'       => 'email',
                'value'      => 'example@email.com',
            ],
            [
                'setting_id' => $settings['kontak']->id,
                'name'       => 'Nomor Telepon',
                'key'        => 'nomor_telepon',
                'type'       => 'number',
                'value'      => '0899999999999',
            ],
        ];
        foreach ($settingItems as $settingItem) {
            SettingItem::updateOrCreate(['name' => $settingItem['name']], $settingItem);
        }
    }
}
