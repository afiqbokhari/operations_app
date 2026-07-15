<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function switch(Request $request)
    {
        $module = $request->module;
        session(['module' => $module]);
        return back();
    }
}
