<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWT
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
        JWTAuth::parseToken()->authenticate();

        if (auth()->user()->is_ban == 1) {
            auth()->logout();
            return response(['message' => 'you are banned'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
