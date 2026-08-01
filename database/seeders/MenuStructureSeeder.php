<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuStructureSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        $menus = [
            ['id' => 1,  'label' => 'Dashboard',   'route_name' => 'dashboard',           'parent_id' => null, 'order' => 1, 'permission' => null,       'is_active' => true, 'module' => 'bookings'],
            ['id' => 2,  'label' => 'Schedule',     'route_name' => 'schedule.index',      'parent_id' => null, 'order' => 2, 'permission' => 'schedule',  'is_active' => true, 'module' => 'bookings'],
            ['id' => 3,  'label' => 'Bookings',     'route_name' => 'bookings.index',      'parent_id' => null, 'order' => 3, 'permission' => 'bookings',  'is_active' => true, 'module' => 'bookings'],
            ['id' => 4,  'label' => 'Events',       'route_name' => 'events.index',        'parent_id' => null, 'order' => 4, 'permission' => 'events',    'is_active' => true, 'module' => 'bookings'],
            ['id' => 12, 'label' => 'Front Desk',   'route_name' => 'front-desk.mail.index', 'parent_id' => null, 'order' => 5, 'permission' => 'front_desk', 'is_active' => true, 'module' => 'front_desk'],
            ['id' => 5,  'label' => 'Management',   'route_name' => null,                    'parent_id' => null, 'order' => 6, 'permission' => null,       'is_active' => true, 'module' => 'bookings'],

            ['id' => 6,  'label' => 'Rooms',        'route_name' => 'rooms.index',          'parent_id' => 5, 'order' => 1, 'permission' => 'rooms',       'is_active' => true, 'module' => 'bookings'],
            ['id' => 7,  'label' => 'Features',     'route_name' => 'features.index',       'parent_id' => 5, 'order' => 2, 'permission' => 'features',   'is_active' => true, 'module' => 'bookings'],
            ['id' => 8,  'label' => 'Users',        'route_name' => 'users.index',          'parent_id' => 5, 'order' => 3, 'permission' => 'users',      'is_active' => true, 'module' => 'bookings'],
            ['id' => 9,  'label' => 'Permissions',  'route_name' => 'permissions.index',    'parent_id' => 5, 'order' => 4, 'permission' => 'permissions','is_active' => true, 'module' => 'bookings'],
            ['id' => 10, 'label' => 'Logs',         'route_name' => 'logs.index',           'parent_id' => 5, 'order' => 5, 'permission' => 'logs',       'is_active' => true, 'module' => 'bookings'],
            ['id' => 11, 'label' => 'Menus',        'route_name' => 'menus.index',          'parent_id' => 5, 'order' => 6, 'permission' => 'menus',      'is_active' => true, 'module' => 'bookings'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}

