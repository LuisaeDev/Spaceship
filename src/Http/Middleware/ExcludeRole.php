<?php

namespace LuisaeDev\Spaceship\Http\Middleware;

use LuisaeDev\Spaceship\Facades\Spaceship;
use Closure;
use Illuminate\Http\Request;

class ExcludeRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $roleName)
    {

        // Check if the user is authenticated
        if (! auth()->check()) {
            abort(401);
        } else {
            $user = auth()->user();
        }

        // Obtain the corresponding space for check
        $spaceId = config('spaceship.middleware-scope') ?? config('spaceship.default-space');
        $space = Spaceship::getSpace($spaceId);

        // Split the roles
        $roleName = explode($roleName, '|');

        // Check if the user has access to the space, and if the role doest not match with any of the specified
        if (($space->canAccess($user)) && ($space->getRole($user)->isNot($roleName))) {
            return $next($request);
        } else {
            abort(403);
        }
    }
}