<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('unauthorize');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        if($jwt = $request->cookie(key: 'jwt')){
            $request->headers->set(key: 'Authorization', values: 'Bearer '.$jwt);
        }
        
        $this->authenticate($request, $guards);

        return $next($request);
    }
}
