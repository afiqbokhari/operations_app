<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class AdminController extends Controller
{
    public function index()
    {
        $sections = Menu::with('children')
            ->where('is_active', true)
            ->where('module', 'admin')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get()
            ->filter(function ($menu) {
                return !$menu->permission || auth()->user()->can($menu->permission . '.view');
            });

        return view('admin.dashboard', compact('sections'));
    }
}
