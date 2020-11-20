<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (app()->environment('local') && ($permission === 'log-viewer' || $permission === 'horizon')) {
            return $next($request);
        }

        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = app('auth')->user();
        if (!app()->environment('local') && ($permission === 'log-viewer' || $permission === 'horizon')) {
            if ($user->is_super_admin) {
                return $next($request);
            }
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if ($user->can($permission) || $user->is_super_admin) {
                return $next($request);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(array('status' => 'Forbidden.'), 403);
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
