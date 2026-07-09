<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('route_name')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('permission')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default menus
        $menus = [
            ['label' => 'Dashboard', 'route_name' => 'dashboard', 'order' => 1, 'permission' => null],
            ['label' => 'Schedule', 'route_name' => 'schedule.index', 'order' => 2, 'permission' => 'schedule'],
            ['label' => 'Rooms', 'route_name' => 'rooms.index', 'order' => 3, 'permission' => 'rooms'],
            ['label' => 'Features', 'route_name' => 'features.index', 'order' => 4, 'permission' => 'features'],
            ['label' => 'Bookings', 'route_name' => 'bookings.index', 'order' => 5, 'permission' => 'bookings'],
            ['label' => 'Events', 'route_name' => 'events.index', 'order' => 6, 'permission' => 'events'],
            ['label' => 'Logs', 'route_name' => 'logs.index', 'order' => 7, 'permission' => 'logs'],
            ['label' => 'Users', 'route_name' => 'users.index', 'order' => 8, 'permission' => 'users'],
            ['label' => 'Permissions', 'route_name' => 'permissions.index', 'order' => 9, 'permission' => 'permissions'],
        ];

        foreach ($menus as $menu) {
            DB::table('menus')->insert(array_merge($menu, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
