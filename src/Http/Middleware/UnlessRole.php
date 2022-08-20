<?php

namespace LuisaeDev\Spaceship\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship;

class UnlessRole
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

            // Check if the user can access to the space, and check if its role is distinct from all those specified.
            if (($space->canAccess($user)) && ($space->getRole($user)->unless(...$roleName))) {
                return $next($request);
            } else {
                abort(403);
            }

        } catch (SpaceshipException $e) {
            abort(500);
        }
    }
}
