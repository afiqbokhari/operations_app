<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events', 'menus'];
        $allAccess = ['view', 'create', 'edit', 'delete'];
        $limitedAccess = ['view', 'create', 'edit'];
        $viewOnly = ['view'];

        $roles = [
            'bd user' => array_fill_keys($routeGroups, $viewOnly),
            'ops user' => $limitedAccess,
            'cofreth hk' => $viewOnly,
            'cofreth fm' => $viewOnly,
            'ops recep' => $limitedAccess,
            'ops admin' => $allAccess,
            'legal' => $viewOnly,
            'finance' => $viewOnly,
        ];

        foreach ($roles as $role => $permissions) {
            DB::table('permissions')->updateOrInsert(
                ['role' => $role],
                [
                    'permissions' => json_encode(array_fill_keys($routeGroups, $permissions)),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
