<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Commands\CreateRoleCommand;
use LuisaeDev\Spaceship\Commands\CreateSpaceCommand;
use LuisaeDev\Spaceship\Contracts\SpaceshipInterface;
use LuisaeDev\Spaceship\Http\Middleware\CanAccess;
use LuisaeDev\Spaceship\Http\Middleware\DifferentRole;
use LuisaeDev\Spaceship\Http\Middleware\OnlyRole;
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
        app('router')->aliasMiddleware('only-role', OnlyRole::class);
        app('router')->aliasMiddleware('diff-role', DifferentRole::class);
    }
}
