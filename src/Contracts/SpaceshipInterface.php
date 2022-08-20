<?php

namespace LuisaeDev\Spaceship\Contracts;

use App\Models\User;
use LuisaeDev\Spaceship\AccessHandler;
use LuisaeDev\Spaceship\RoleHandler;
use LuisaeDev\Spaceship\SpaceHandler;

interface SpaceshipInterface
{
    /**
     * Create a new space model.
     *
     * @param  string|array  $data Space name or attributes to add for the new space model
     * @return SpaceHandler Corresponding space handler instance
     */
    public function createSpace(string|array $data): SpaceHandler;

    /**
     * Get a space handler instance.
     *
     * @param  int|string  $spaceId Space identifier
     * @return SpaceHandler
     */
    public function getSpace(int|string $spaceId): SpaceHandler;

    /**
     * Create a new role model.
     *
     * @param  string  $name Unique name for the role
     * @return RoleHandler Corresponding role handler instance
     */
    public static function createRole(string $name): RoleHandler;

    /**
     * Get a role handler instance.
     *
     * @param  int|string  $roleId Role identifier
     * @return RoleHandler
     */
    public function getRole(int|string $roleId): RoleHandler;

    /**
     * Create a new access model.
     *
     * @param  SpaceHandler|int|string  $space
     * @param  User  $user
     * @param  RoleHandler|string  $role
     * @return AccessHandler Corresponding access handler instance
     */
    public function createAccess(SpaceHandler|int|string $space, User $user, RoleHandler|string $role): AccessHandler;

    /**
     * Get an access handler instance.
     *
     * @param  int|string  $accessId Access identifier
     * @return AccessHandler
     */
    public function getAccess(int|string $accessId): AccessHandler;
}
