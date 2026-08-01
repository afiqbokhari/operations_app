<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function switch(Request $request)
    {
        $module = $request->module;
        session(['module' => $module]);

        $redirects = [
            'bookings' => route('dashboard'),
            'front_desk' => route('front-desk.mail.index'),
        ];

        return redirect($redirects[$module] ?? route('dashboard'));
    }
}
