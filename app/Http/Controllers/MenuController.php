<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        $allMenus = Menu::orderBy('label')->get();
        $permissions = ['schedule', 'rooms', 'features', 'bookings', 'events', 'logs', 'users', 'permissions', 'menus', 'front_desk'];
        return view('menus.index', compact('menus', 'allMenus', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'route_name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer|min:0',
            'permission' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        Menu::create($validated);
        return back()->with('success', 'Menu added.');
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'route_name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer|min:0',
            'permission' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $menu->update($validated);
        return back()->with('success', 'Menu updated.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu deleted.');
    }
}
