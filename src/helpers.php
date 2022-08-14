<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Facades\Spaceship;

if (! function_exists('space')) {

    /**
     * Get a space handler instance.
     *
     * @param  int|string|null  $spaceId Space identifier. When null, default space will be defined
     * @return SpaceHandler
     */
    function space(int|string|null $spaceId = null): SpaceHandler
    {
        return Spaceship::getSpace($spaceId);
    }
}

if (! function_exists('role')) {

    /**
     * Get a role handler instance.
     *
     * @param  int|string  $roleId Role identifier
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
     * @param  int|string  $accessId Access identifier
     * @return AccessHandler
     */
    function access(int|string $accessId): AccessHandler
    {
        return Spaceship::getAccess($accessId);
    }
}
