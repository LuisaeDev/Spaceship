<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Contracts\SpaceshipInterface;
use LuisaeDev\Spaceship\Http\Middleware\CanAccess;
use LuisaeDev\Spaceship\Http\Middleware\OnlyRole;
use LuisaeDev\Spaceship\Http\Middleware\DifferentRole;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpaceshipServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('spaceship')
            ->hasConfigFile('spaceship')
            ->hasMigration('create_spaceship_spaces_table')
            ->hasMigration('create_spaceship_roles_table')
            ->hasMigration('create_spaceship_accesses_table');
    }

    public function registeringPackage()
    {

        // Spaceship facades
        app()->bind('spaceship', function () {
            return new Spaceship();
        });

        // Spaceship contracts
        app()->bind(SpaceshipInterface::class, Spaceship::class);
    }

    public function bootingPackage()
    {
        // Middlewares regitration
        app('router')->aliasMiddleware('can-access', CanAccess::class);
        app('router')->aliasMiddleware('only-role', OnlyRole::class);
        app('router')->aliasMiddleware('diff-role', DifferentRole::class);
    }
}
