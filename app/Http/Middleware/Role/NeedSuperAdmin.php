<?php

namespace App\Http\Middleware\Role;

use Closure;
use Illuminate\Http\Response;

class NeedSuperAdmin
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
        $me = auth()->user();

        if ($me->hasRole('super_admin')) {
            return $next($request);
        }

        return response(['error' => config('app.role_error')], Response::HTTP_FORBIDDEN);
    }
}
