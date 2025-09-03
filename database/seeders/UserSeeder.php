<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['username' => 'superadmin', 'name' => 'Superadmin', 'nomor_whatsapp' => null],
            ['username' => 'admin', 'name' => 'Admin', 'nomor_whatsapp' => null],
            ['username' => 'pimpinan', 'name' => 'Pimpinan', 'nomor_whatsapp' => null],
            ['username' => 'perencana', 'name' => 'Perencana', 'nomor_whatsapp' => null],
            ['username' => 'subbagian', 'name' => 'Subbagian', 'nomor_whatsapp' => '6289508475453'],
        ];

        foreach ($data as $item) {
            User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name'           => $item['name'],
                    'username'       => $item['username'],
                    'email'          => $item['username'] . '@email.com',
                    'password'       => bcrypt('password'),
                    'nip'            => str_pad((string)rand(0, 999999999999999999), 18, '0', STR_PAD_LEFT),
                    'nomor_whatsapp' => $item['nomor_whatsapp'],
                ]
            );
        }
    }
}
