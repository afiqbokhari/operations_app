<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Permission;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $routeGroup, string $action = 'view')
    {
        if (!auth()->check()) {
            abort(403);
        }

        $role = auth()->user()->role;

        // Map HTTP methods to actions
        if ($action === 'auto') {
            $action = match($request->method()) {
                'GET' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'edit',
                'DELETE' => 'delete',
                default => 'view',
            };
        }

        if (!Permission::can($role, $routeGroup, $action)) {
            abort(403, "Unauthorized. Role '{$role}' cannot {$action} {$routeGroup}.");
        }

        return $next($request);
    }
}
