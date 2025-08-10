<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create roles 
        $RoleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $RoleAdmin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $RolePimpinan   = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);
        $RolePerencana  = Role::firstOrCreate(['name' => 'perencana', 'guard_name' => 'web']);
        $RoleSubbagian  = Role::firstOrCreate(['name' => 'subbagian', 'guard_name' => 'web']);

        // get permissions
        $permissions = Permission::pluck('name')->toArray();

        $pimpinanPermissions  = [];
        $perencanaPermissions = [
            'view-any Dokumen',
            'view Dokumen',
            'create Dokumen',
            'update Dokumen',
            'delete Dokumen',
            'delete-any Dokumen',
            'reorder Dokumen',
            'replicate Dokumen',
            'restore Dokumen',
            'restore-any Dokumen',
            'force-delete Dokumen',
            'force-delete-any Dokumen',

            'view-any FileDokumen',
            'view FileDokumen',
            'create FileDokumen',
            'update FileDokumen',
            'delete FileDokumen',
            'delete-any FileDokumen',
            'reorder FileDokumen',
            'replicate FileDokumen',
            'restore FileDokumen',
            'restore-any FileDokumen',
            'force-delete FileDokumen',
            'force-delete-any FileDokumen',

            'view-any Panduan',
            'view Panduan',
            // 'create Panduan',
            // 'update Panduan',
            // 'delete Panduan',
            // 'delete-any Panduan',
            // 'reorder Panduan',
            // 'replicate Panduan',
            // 'restore Panduan',
            // 'restore-any Panduan',
            // 'force-delete Panduan',
            // 'force-delete-any Panduan',

            'view-any Faq',
            'view Faq',
            // 'create Faq',
            // 'update Faq',
            // 'delete Faq',
            // 'delete-any Faq',
            // 'reorder Faq',
            // 'replicate Faq',
            // 'restore Faq',
            // 'restore-any Faq',
            // 'force-delete Faq',
            // 'force-delete-any Faq',

            'view-any Pengaduan',
            'view Pengaduan',
            'create Pengaduan',
            // 'update Pengaduan',
            // 'delete Pengaduan',
            // 'delete-any Pengaduan',
            // 'reorder Pengaduan',
            // 'replicate Pengaduan',
            // 'restore Pengaduan',
            // 'restore-any Pengaduan',
            // 'force-delete Pengaduan',
            // 'force-delete-any Pengaduan',
        ];
        $subbagianPermissions = [
            'view-any Dokumen',
            'view Dokumen',
            // 'create Dokumen',
            'update Dokumen',
            // 'delete Dokumen',
            // 'delete-any Dokumen',
            // 'reorder Dokumen',
            // 'replicate Dokumen',
            // 'restore Dokumen',
            // 'restore-any Dokumen',
            // 'force-delete Dokumen',
            // 'force-delete-any Dokumen',

            'view-any FileDokumen',
            'view FileDokumen',
            'create FileDokumen',
            // 'update FileDokumen',
            // 'delete FileDokumen',
            // 'delete-any FileDokumen',
            // 'reorder FileDokumen',
            // 'replicate FileDokumen',
            // 'restore FileDokumen',
            // 'restore-any FileDokumen',
            // 'force-delete FileDokumen',
            // 'force-delete-any FileDokumen',

            'view-any Panduan',
            'view Panduan',
            // 'create Panduan',
            // 'update Panduan',
            // 'delete Panduan',
            // 'delete-any Panduan',
            // 'reorder Panduan',
            // 'replicate Panduan',
            // 'restore Panduan',
            // 'restore-any Panduan',
            // 'force-delete Panduan',
            // 'force-delete-any Panduan',

            'view-any Faq',
            'view Faq',
            // 'create Faq',
            // 'update Faq',
            // 'delete Faq',
            // 'delete-any Faq',
            // 'reorder Faq',
            // 'replicate Faq',
            // 'restore Faq',
            // 'restore-any Faq',
            // 'force-delete Faq',
            // 'force-delete-any Faq',

            'view-any Pengaduan',
            'view Pengaduan',
            'create Pengaduan',
            // 'update Pengaduan',
            // 'delete Pengaduan',
            // 'delete-any Pengaduan',
            // 'reorder Pengaduan',
            // 'replicate Pengaduan',
            // 'restore Pengaduan',
            // 'restore-any Pengaduan',
            // 'force-delete Pengaduan',
            // 'force-delete-any Pengaduan',
        ];

        // set permissions for role  
        $RoleSuperAdmin->syncPermissions($permissions);
        $RoleAdmin->syncPermissions($permissions);
        $RolePimpinan->syncPermissions($pimpinanPermissions);
        $RolePerencana->syncPermissions($perencanaPermissions);
        $RoleSubbagian->syncPermissions($subbagianPermissions);

        // set role for users
        $roles = [
            'superadmin' => 'Super Admin',
            'admin'      => 'admin',
            'pimpinan'   => 'pimpinan',
            'perencana'  => 'perencana',
            'subbagian'  => 'subbagian',
        ];
        foreach ($roles as $username => $role) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }
    }
}
