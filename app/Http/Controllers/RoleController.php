<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $protectedRoles = ['administrator', 'admin', 'manager', 'staff'];

    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['role' => 'required|string|max:50']);

        Role::findOrCreate($request->role, 'web');

        return back()->with('success', 'Role added.');
    }

    public function destroy($role)
    {
        if (in_array(strtolower($role), $this->protectedRoles)) {
            return back()->with('error', 'Cannot delete protected role: ' . $role);
        }

        $roleModel = Role::where('name', $role)->first();
        if ($roleModel) {
            $roleModel->delete();
        }

        return back()->with('success', 'Role deleted.');
    }
}
