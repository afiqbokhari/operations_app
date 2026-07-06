<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['role', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public static function can(string $role, string $routeGroup, string $action = 'view'): bool
    {
        $perm = static::where('role', $role)->first();
        if (!$perm) return false;

        return in_array($action, $perm->permissions[$routeGroup] ?? []);
    }
}
