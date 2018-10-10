<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Closure;

class AuthUserSetting
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
        $guard = 'user';

        config(['auth.defaults.guard' => $guard]);

        try {
            $user = auth()->guard($guard)->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            throw new UnauthorizedHttpException('jwt-auth', 'Token is invalid');
        }  
        return $next($request);        
    }
}
