<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role')->unique();
            $table->json('permissions'); // {"users":["view","create","edit","delete"], "rooms":["view","create","edit","delete"], ...}
            $table->timestamps();
        });

        // Seed default permissions
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs'];
        $allAccess = ['view', 'create', 'edit', 'delete'];

        $defaults = [
            'admin' => array_fill_keys($routeGroups, $allAccess),
            'manager' => array_fill_keys($routeGroups, ['view', 'create', 'edit']),
            'staff' => array_fill_keys($routeGroups, ['view', 'create', 'edit']),
        ];

        foreach ($defaults as $role => $perms) {
            DB::table('permissions')->insert([
                'role' => $role,
                'permissions' => json_encode($perms),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
