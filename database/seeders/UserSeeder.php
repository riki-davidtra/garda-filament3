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
            ['username' => 'superadmin', 'name' => 'Superadmin', 'role' => 'Super Admin', 'subbagian_id' => null],
            ['username' => 'admin', 'name' => 'Admin', 'role' => 'admin', 'subbagian_id' => null],
            ['username' => 'pimpinan', 'name' => 'Pimpinan', 'role' => 'pimpinan', 'subbagian_id' => null],
            ['username' => 'perencana', 'name' => 'Perencana', 'role' => 'perencana', 'subbagian_id' => null],
            ['username' => 'subbagian', 'name' => 'Subbagian', 'role' => 'subbagian', 'subbagian_id' => 1],
        ];

        foreach ($data as $item) {
            $user = User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name'     => $item['name'],
                    'username' => $item['username'],
                    'email'    => $item['username'] . '@email.com',
                    'password' => bcrypt('password'),
                    'subbagian_id' => $item['subbagian_id'],
                    'nip'          => str_pad((string)rand(0, 999999999999999999), 18, '0', STR_PAD_LEFT),
                ]
            );

            if (! empty($item['role'])) {
                $user->assignRole($item['role']);
            }
        }
    }
}
