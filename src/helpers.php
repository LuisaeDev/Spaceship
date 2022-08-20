<?php

use LuisaeDev\Spaceship\AccessHandler;
use LuisaeDev\Spaceship\Facades\Spaceship;
use LuisaeDev\Spaceship\RoleHandler;
use LuisaeDev\Spaceship\SpaceHandler;

if (! function_exists('space')) {

    /**
     * Get a space handler instance.
     *
     * @param int|string $spaceId Space identifier.
     * @return SpaceHandler
     */
    function space(int|string $spaceId): SpaceHandler
    {
        return Spaceship::getSpace($spaceId);
    }
}

if (! function_exists('role')) {

    /**
     * Get a role handler instance.
     *
     * @param int|string $roleId Role identifier
     * @return RoleHandler
     */
    function role(int|string $roleId): RoleHandler
    {
        return Spaceship::getRole($roleId);
    }
}

if (! function_exists('access')) {

    /**
     * Get an access handler instance.
     *
     * @param int|string $accessId Access identifier
     * @return AccessHandler
     */
    function access(int|string $accessId): AccessHandler
    {
        return Spaceship::getAccess($accessId);
    }
}

if (! function_exists('hasAccess')) {

    /**
     * Check if the current user has access to a spacific space.
     *
     * @param SpaceHandler|int|string $space
     * @return bool
     */
    function hasAccess(SpaceHandler|int|string $space): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return auth()->user()->hasAccess($space);
    }
}

if (! function_exists('canAccess')) {

    /**
     * Check if the current user can access to a specific space.
     *
     * @param SpaceHandler|int|string $space
     * @return bool
     */
    function canAccess(SpaceHandler|int|string $space): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return auth()->user()->canAccess($space);
    }
}

if (! function_exists('canAccessAny')) {

    /**
     * Check if the current user can access to any of multiple specificied spaces.
     *
     * @param SpaceHandler|int|string ...$spaces
     * @return boolean
     */
    function canAccessAny(SpaceHandler|int|string ...$spaces): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return auth()->user()->canAccessAny(...$spaces);
    }
}

if (! function_exists('canAccessAll')) {

    /**
     * Check if the current user can access to all of multiple specificied spaces.
     *
     * @param SpaceHandler|int|string ...$spaces
     * @return boolean
     */
    function canAccessAll(SpaceHandler|int|string ...$spaces): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return auth()->user()->canAccessAll(...$spaces);
    }
}

if (! function_exists('unlessAccess')) {

    /**
     * Check if the current user can not access to all of multiple specificied spaces.
     *
     * @param SpaceHandler|int|string ...$spaces
     * @return boolean
     */
    function unlessAccess(SpaceHandler|int|string ...$spaces): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return auth()->user()->unlessAccess(...$spaces);
    }
}

if (! function_exists('accessFrom')) {

    /**
     * For the current user, return the access related to the specific space.
     *
     * @param SpaceHandler|int|string $space
     * @return AccessHandler|null
     */
    function accessFrom(SpaceHandler|int|string $space): ?AccessHandler
    {
        if (! auth()->check()) {
            return null;
        }
        return auth()->user()->accessFrom($space);
    }

}

if (! function_exists('roleFrom')) {

    /**
     * For the current user, return the role related to the specific space.
     *
     * @param SpaceHandler|int|string $space
     * @return RoleHandler|null
     */
    function roleFrom(SpaceHandler|int|string $space): ?RoleHandler
    {
        if (! auth()->check()) {
            return null;
        }
        return auth()->user()->roleFrom($space);
    }    
}
