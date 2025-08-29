<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $seeders = [
            UserSeeder::class,
            SyncPermissionsSeeder::class,
            RolePermissionSeeder::class,
            SettingSeeder::class,
            BagianSeeder::class,
            SubkegiatanSeeder::class,
            FormatFileSeeder::class,
            JenisDokumenSeeder::class,
            JadwalDokumenSeeder::class,
            DokumenSeeder::class,
            PanduanSeeder::class,
            FaqSeeder::class,
            TemplatDokumenSeeder::class,
            IndikatorSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $basename = class_basename($seeder);
            if (\Illuminate\Support\Facades\Config::get("seeder.skip.{$basename}", false) === true) {
                continue;
            }
            $this->call($seeder);
        }
    }
}
