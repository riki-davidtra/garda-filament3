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
            ['username' => 'superadmin', 'name' => 'Superadmin'],
            ['username' => 'admin', 'name' => 'Admin'],
            ['username' => 'pimpinan', 'name' => 'Pimpinan'],
            ['username' => 'perencana', 'name' => 'Perencana'],
            ['username' => 'subbagian', 'name' => 'Subbagian'],
        ];

        foreach ($data as $item) {
            $user = User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name'     => $item['name'],
                    'username' => $item['username'],
                    'email'    => $item['username'] . '@email.com',
                    'password' => bcrypt('password'),
                    'nip'      => str_pad((string)rand(0, 999999999999999999), 18, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
