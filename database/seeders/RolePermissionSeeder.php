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

        // 'view-any Dokumen',
        // 'view Dokumen',
        // 'create Dokumen',
        // 'update Dokumen',
        // 'delete Dokumen',
        // 'delete-any Dokumen',
        // 'reorder Dokumen',
        // 'replicate Dokumen',
        // 'restore Dokumen',
        // 'restore-any Dokumen',
        // 'force-delete Dokumen',
        // 'force-delete-any Dokumen',

        $perencanaPermissions = [
            'view-any Dokumen',
            'view Dokumen',
            'create Dokumen',
            'update Dokumen',

            'view-any FileDokumen',
            'view FileDokumen',

            'view-any Panduan',
            'view Panduan',

            'view-any Faq',
            'view Faq',

            'view-any Pengaduan',
            'view Pengaduan',
            'create Pengaduan',

            'view-any TemplatDokumen',
            'view TemplatDokumen',

            'view-any IndeksKinerjaUtama',
            'view IndeksKinerjaUtama',
            'create IndeksKinerjaUtama',
            'update IndeksKinerjaUtama',

            'view-any DataDukungPerencanaan',
            'view DataDukungPerencanaan',
            'create DataDukungPerencanaan',
            'update DataDukungPerencanaan',
        ];
        $subbagianPermissions = [
            'view-any Dokumen',
            'view Dokumen',
            'create Dokumen',
            'update Dokumen',

            'view-any FileDokumen',
            'view FileDokumen',
            'create FileDokumen',

            'view-any Panduan',
            'view Panduan',

            'view-any Faq',
            'view Faq',

            'view-any Pengaduan',
            'view Pengaduan',
            'create Pengaduan',

            'view-any TemplatDokumen',
            'view TemplatDokumen',
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
