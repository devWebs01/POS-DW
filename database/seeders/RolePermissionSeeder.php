<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionsByGroup = [
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'transactions' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view'],
            'settings' => ['store', 'profile', 'security'],
        ];

        $permissions = collect();

        foreach ($permissionsByGroup as $group => $actions) {
            foreach ($actions as $action) {
                $name = "{$group}.{$action}";
                $permissions->push($name);
                Permission::findOrCreate($name);
            }
        }

        $superAdmin = Role::findOrCreate('super-admin');
        $superAdmin->syncPermissions($permissions);

        $pemilik = Role::findOrCreate('pemilik');
        $pemilik->syncPermissions(
            $permissions->filter(fn ($p) => ! str($p)->startsWith('roles.')
                && ! str($p)->startsWith('permissions.')
            )
        );

        $kasir = Role::findOrCreate('kasir');
        $kasir->syncPermissions(
            $permissions->filter(fn ($p) => str($p)->startsWith('transactions.')
                || $p === 'settings.profile'
            )
        );

        $adminUser = User::where('email', 'admin@testing.com')->first();
        $pemilikUser = User::where('email', 'admin@testing.com')->first();
        $kasirUser = User::where('email', 'kasir@testing.com')->first();

        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }

        if ($adminUser) {
            $adminUser->assignRole('pemilik');
        }

        if ($kasirUser) {
            $kasirUser->assignRole('kasir');
        }
    }
}
