<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('role')->get();
        $routeGroups = ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions'];
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
