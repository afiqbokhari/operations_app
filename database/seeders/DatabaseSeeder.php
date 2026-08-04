<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FeatureSeeder::class,
            RoomSeeder::class,
            RoomFeatureSeeder::class,
            MenuStructureSeeder::class,
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
