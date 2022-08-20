<?php

namespace LuisaeDev\Spaceship;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Compilers\BladeCompiler;
use LuisaeDev\Spaceship\Commands\CreateRoleCommand;
use LuisaeDev\Spaceship\Commands\CreateSpaceCommand;
use LuisaeDev\Spaceship\Contracts\SpaceshipInterface;
use LuisaeDev\Spaceship\Http\Middleware\AnyRole;
use LuisaeDev\Spaceship\Http\Middleware\CanAccess;
use LuisaeDev\Spaceship\Http\Middleware\UnlessRole;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpaceshipServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('spaceship')
            ->hasConfigFile('spaceship')
            ->hasCommand(CreateSpaceCommand::class)
            ->hasCommand(CreateRoleCommand::class);
    }

    public function registeringPackage()
    {

        // Register spaceship's blade directives
        $this->callAfterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $this->registerBladeExtensions($bladeCompiler);
        });

        // Spaceship facade
        app()->bind(Spaceship::class, function () {
            return new Spaceship();
        });

        // Spaceship contracts
        app()->bind(SpaceshipInterface::class, Spaceship::class);
    }

    public function bootingPackage()
    {

        // Load and publish migrations
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'spaceship-migrations');
        }

        // Middlewares regitration
        app('router')->aliasMiddleware('can-access', CanAccess::class);
        app('router')->aliasMiddleware('any-role', AnyRole::class);
        app('router')->aliasMiddleware('unless-role', UnlessRole::class);
    }

    private function registerBladeExtensions($bladeCompiler)
    {
        $bladeCompiler->if('hasAccess', function ($space) {
            if (! Auth::check()) {
                return false;
            }

            return Auth::user()->hasAccess($space);
        });

        $bladeCompiler->if('canAccess', function ($space) {
            if (! Auth::check()) {
                return false;
            }

            return Auth::user()->canAccess($space);
        });

        $bladeCompiler->if('canAccessAny', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            return Auth::user()->canAccessAny(...$spaces);
        });

        $bladeCompiler->if('canAccessAll', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            return Auth::user()->canAccessAll(...$spaces);
        });

        $bladeCompiler->if('unlessAccess', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            return Auth::user()->unlessAccess(...$spaces);
        });

        $bladeCompiler->if('hasRole', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            // Iterate all spaces
            foreach ($spaces as $space) {

                // Split the space and roles
                [$space, $roles] = explode(':', $space);
                $roles = explode('|', $roles);

                // Obtain the access role
                $role = Auth::user()->roleFrom($space);

                // If role exists, check if any match
                if (($role) && ($role->isAny(...$roles))) {
                    return true;
                }
            }

            return false;
        });

        $bladeCompiler->if('unlessRole', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            // Iterate all spaces
            foreach ($spaces as $space) {

                // Split the space and roles
                [$space, $roles] = explode(':', $space);
                $roles = explode('|', $roles);

                // Obtain the access role
                $role = Auth::user()->roleFrom($space);

                // If role exists, check if any match
                if (($role) && ($role->isAny(...$roles))) {
                    return false;
                }
            }

            return true;
        });

        $bladeCompiler->if('roleCan', function ($space) {
            if (! Auth::check()) {
                return false;
            }

            // Split the space and actions
            [$space, $actions] = explode(':', $space);
            $actions = explode('|', $actions);

            // Obtain the access role
            $role = Auth::user()->roleFrom($space);

            // If role exists, check if can perform the specified actions
            if (($role) && ($role->can(...$actions))) {
                return true;
            }

            return false;
        });

        $bladeCompiler->if('anyRoleCan', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            // Iterate all spaces
            foreach ($spaces as $space) {

                // Split the space and actions
                [$space, $actions] = explode(':', $space);
                $actions = explode('|', $actions);

                // Obtain the access role
                $role = Auth::user()->roleFrom($space);

                // If role exists, check if can perform the specified actions
                if (($role) && ($role->can(...$actions))) {
                    return true;
                }
            }

            return false;
        });

        $bladeCompiler->if('allRoleCan', function (...$spaces) {
            if (! Auth::check()) {
                return false;
            }

            // Iterate all spaces
            foreach ($spaces as $space) {

                // Split the space and actions
                [$space, $actions] = explode(':', $space);
                $actions = explode('|', $actions);

                // Obtain the access role
                $role = Auth::user()->roleFrom($space);

                // If role exists, check if can not perform the specified actions
                if ((! $role) || (! $role->can(...$actions))) {
                    return false;
                }
            }

            return true;
        });
    }
}
