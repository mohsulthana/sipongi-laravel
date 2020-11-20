<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $horizonUrl = config('horizon.path');
        $logViewer = config('log-viewer.route.attributes.prefix');
        $currentUrl = $request->url();
        $matches = preg_match("#^.*($logViewer|$horizonUrl).*\z#u", $currentUrl);
        $isLocal = app()->environment('local');

        if ($matches && $isLocal) {
            return $next($request);
        }

        $this->authenticate($request, $guards);
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return '/login';
        }
    }
}
