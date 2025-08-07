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
        $RolePerencana  = Role::firstOrCreate(['name' => 'perencana', 'guard_name' => 'web']);
        $RoleSubbagian  = Role::firstOrCreate(['name' => 'subbagian', 'guard_name' => 'web']);
        $RolePimpinan   = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);

        // get permissions
        $permissions          = Permission::pluck('name')->toArray();
        $perencanaPermissions = [
            'view-any Model',
            'view Model',
            'create Model',
            'update Model',
        ];
        foreach ($perencanaPermissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web'
            ]);
        }

        // set permissions for role
        $RoleSuperAdmin->syncPermissions($permissions);
        $RoleAdmin->syncPermissions($permissions);
        $RolePerencana->syncPermissions($perencanaPermissions);

        // set role for users
        $roles = [
            'superadmin' => 'Super Admin',
            'admin'      => 'admin',
            'user'       => 'user',
        ];
        foreach ($roles as $username => $role) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }
    }
}
