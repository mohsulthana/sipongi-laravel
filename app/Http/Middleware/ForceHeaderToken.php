<?php

namespace App\Http\Middleware;

// use Auth,JWTAuth;
use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ForceHeaderToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->filled('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->get('token'));
        }

        $horizonUrl = config('horizon.path');
        $logViewer = config('log-viewer.route.attributes.prefix');
        $currentUrl = $request->url();
        $matches = preg_match("#^.*($logViewer|$horizonUrl).*\z#u", $currentUrl);
        if ($matches) {
            $request->headers->set('Authorization', 'Bearer ' . Cookie::get('__tn'));
        }

        return $next($request);
    }
}
