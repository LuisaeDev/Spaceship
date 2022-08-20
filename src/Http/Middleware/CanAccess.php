<?php

namespace LuisaeDev\Spaceship\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CanAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $space)
    {

        // Check if the user is authenticated
        if (! auth()->check()) {
            abort(401);
        } else {
            $user = auth()->user();
        }

        // Check if the user can access to the space
        if (! $user->canAccess($space)) {
            abort(403);
        }

        // Store the space name at config for further validations at Spaceship's middlewares
        Config::set('spaceship.middleware-scope', $space);

        return $next($request);
    }
}
