<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events', 'menus', 'front_desk'];
        $actions = ['view', 'create', 'edit', 'delete'];

        // Create every permission name as <group>.<action>, e.g. bookings.view
        foreach ($routeGroups as $group) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$group}.{$action}", 'web');
            }
        }

        // Role definitions: role => allowed actions applied to every route group
        $allAccess = ['view', 'create', 'edit', 'delete'];
        $limitedAccess = ['view', 'create', 'edit'];
        $viewOnly = ['view'];

        $roles = [
            'admin'       => $allAccess,
            'ops admin'   => $allAccess,
            'ops_admin'   => $allAccess,
            'ops user'    => $limitedAccess,
            'ops_user'    => $limitedAccess,
            'ops recep'   => $limitedAccess,
            'staff'       => $limitedAccess,
            'bd user'     => $viewOnly,
            'cofreth hk'  => $viewOnly,
            'cofreth fm'  => $viewOnly,
            'legal'       => $viewOnly,
            'finance'     => $viewOnly,
        ];

        foreach ($roles as $role => $roleActions) {
            $roleModel = Role::findOrCreate($role, 'web');

            $permissions = collect($routeGroups)
                ->flatMap(fn ($group) => array_map(fn ($action) => "{$group}.{$action}", $roleActions))
                ->all();

            $roleModel->syncPermissions($permissions);
        }

        // Assign roles to existing users based on their legacy role column
        foreach (User::all() as $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            }
        }
    }
}
