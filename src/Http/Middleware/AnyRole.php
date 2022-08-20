<?php

namespace LuisaeDev\Spaceship\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship;

class AnyRole
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
        $spaceId = config('spaceship.middleware-scope');

        // Split the roles
        $roleName = explode('|', $roleName);

        try {
            $space = Spaceship::getSpace($spaceId);

            // Check if the user can access to the space, and if any role matchs with one of the specifieded
            if (($user->canAccess($space)) && ($user->roleFrom($space)->isAny(...$roleName))) {
                return $next($request);
            } else {
                abort(403);
            }
        } catch (SpaceshipException $e) {
            abort(500);
        }
    }
}
