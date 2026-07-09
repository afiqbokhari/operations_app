<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        // Ensure all existing roles have a permission record
        $roles = User::distinct()->pluck('role');
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events'];

        foreach ($roles as $role) {
            if (!Permission::where('role', $role)->exists()) {
                Permission::create([
                    'role' => $role,
                    'permissions' => array_fill_keys($routeGroups, ['view']),
                ]);
            }
        }

        $permissions = Permission::orderBy('role')->get();
        $actions = ['view', 'create', 'edit', 'delete'];

        return view('permissions.index', compact('permissions', 'routeGroups', 'actions'));
    }

    public function update(Request $request, Permission $permission)
    {
        $permissions = $request->input('permissions', []);
        $permission->update(['permissions' => $permissions]);

        return redirect()->route('permissions.index')->with('success', 'Permissions updated for ' . $permission->role . '.');
    }
}
