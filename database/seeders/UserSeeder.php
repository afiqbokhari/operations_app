<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@aiac.local'],
            ['name' => 'Admin User', 'password' => Hash::make('password123')]
        );
        $admin->syncRoles(['admin']);

        $staff = User::updateOrCreate(
            ['email' => 'staff@aiac.local'],
            ['name' => 'Staff User', 'password' => Hash::make('password123')]
        );
        $staff->syncRoles(['staff']);
    }
}
