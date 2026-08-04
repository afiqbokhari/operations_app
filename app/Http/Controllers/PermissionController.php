<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events', 'menus', 'front_desk'];
        $actions = ['view', 'create', 'edit', 'delete'];

        return view('permissions.index', compact('roles', 'routeGroups', 'actions'));
    }

    public function update(Request $request, Role $role)
    {
        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('permissions.index')->with('success', 'Permissions updated for ' . $role->name . '.');
    }
}
