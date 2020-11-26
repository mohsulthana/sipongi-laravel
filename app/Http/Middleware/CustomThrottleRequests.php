<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;

class CustomThrottleRequests extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        $key = $prefix.$this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {

            $retryAfter = $this->getTimeUntilNextRetry($key);
            if ($request->wantsJson()) {
                return response()->json(array(
                    'status' => 'to_many_request', 
                    'message' => __('auth.throttle', ['seconds' => $retryAfter]),
                    'retryAfter' => $retryAfter
                ), 429);
            }
            throw $this->buildException($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature($request)
    {
        if ($user = $request->user()) {
            return sha1($request->path().'|'.$user->getAuthIdentifier().'|'.$request->ip());
        }

        if ($request->route()) {
            return sha1($request->header('origin').'|'.$request->path().'|'.$request->ip());
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }
}
