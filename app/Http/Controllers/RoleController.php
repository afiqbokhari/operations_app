<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RoleController extends Controller
{
    protected $protectedRoles = ['Administrator', 'Admin', 'admin', 'Manager', 'manager', 'Staff', 'staff'];

    public function index()
    {
        $roles = \DB::table('permissions')->orderBy('role')->pluck('role')->toArray();
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['role' => 'required|string|max:50|unique:permissions,role']);

        DB::table('permissions')->insert([
            'role' => $request->role,
            'permissions' => json_encode(array_fill_keys(
                ['users', 'rooms', 'bookings', 'features', 'schedule', 'logs', 'permissions', 'events', 'menus'],
                ['view']
            )),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Role added.');
    }

    public function destroy($role)
    {
        if (in_array($role, $this->protectedRoles)) {
            return back()->with('error', 'Cannot delete protected role: ' . $role);
        }
        DB::table('permissions')->where('role', $role)->delete();
        return back()->with('success', 'Role deleted.');
    }
}