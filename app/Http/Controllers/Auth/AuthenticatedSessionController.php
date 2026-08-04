<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Determine the user's first accessible module (in priority order) so
        // they are redirected to the correct home page after login.
        $module = null;
        foreach (['bookings', 'front_desk'] as $id) {
            if ($user->can($id . '.view')) {
                $module = $id;
                break;
            }
        }

        if ($module) {
            $request->session()->put('module', $module);
        }

        $home = match ($module) {
            'front_desk' => route('front-desk.dashboard'),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($home);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
