<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events', 'menus'];
        $allAccess = ['view', 'create', 'edit', 'delete'];

        DB::table('permissions')->where('role', 'admin')->update([
            'permissions' => json_encode(array_fill_keys($routeGroups, $allAccess)),
        ]);

        DB::table('permissions')->whereIn('role', ['manager', 'staff'])->update([
            'permissions' => json_encode(array_fill_keys($routeGroups, ['view', 'create', 'edit'])),
        ]);
    }

    public function down(): void
    {
        // No need to revert
    }
};
