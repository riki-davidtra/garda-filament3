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
            ['username' => 'superadmin', 'name' => 'Superadmin', 'subbagian_id' => null],
            ['username' => 'admin', 'name' => 'Admin', 'subbagian_id' => null],
            ['username' => 'pimpinan', 'name' => 'Pimpinan', 'subbagian_id' => null],
            ['username' => 'perencana', 'name' => 'Perencana', 'subbagian_id' => null],
            ['username' => 'subbagian', 'name' => 'Subbagian', 'subbagian_id' => 1],
        ];

        foreach ($data as $item) {
            User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name'         => $item['name'],
                    'username'     => $item['username'],
                    'email'        => $item['username'] . '@email.com',
                    'password'     => bcrypt('password'),
                    'subbagian_id' => $item['subbagian_id'],
                ]
            );
        }
    }
}
