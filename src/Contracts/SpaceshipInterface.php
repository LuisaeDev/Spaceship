<?php

namespace LuisaeDev\Spaceship\Contracts;

use LuisaeDev\Spaceship\SpaceHandler;
use LuisaeDev\Spaceship\RoleHandler;
use LuisaeDev\Spaceship\AccessHandler;
use App\Models\User;

interface SpaceshipInterface
{

    /**
     * Create a new space model.
     *
     * @param string|array $data Space name or attributes to add for the new space model
     * @return SpaceHandler Corresponding space handler instance
     */
    public function createSpace(string|array $data): SpaceHandler;

    /**
     * Get a space handler instance.
     *
     * @param int|string|null $spaceId Space identifier. When null, default space will be defined
     * @return SpaceHandler
     */
    public function getSpace(int|string|null $spaceId = null): SpaceHandler;

    /**
     * Create a new role model.
     *
     * @param string $name Unique name for the role
     * @return RoleHandler Corresponding role handler instance
     */
    static public function createRole(string $name): RoleHandler;

    /**
     * Get a role handler instance.
     *
     * @param int|string $roleId Role identifier for obtain the role model
     * @return RoleHandler
     */
    public function getRole(int|string $roleId): RoleHandler;

    /**
     * Create a new access model.
     *
     * @param SpaceHandler $space
     * @param User $user
     * @param RoleHandler|string $role
     * @return AccessHandler Corresponding access handler instance
     */
    public function createAccess(SpaceHandler $space, User $user, RoleHandler|string $role): AccessHandler;

    /**
     * Get an access handler instance.
     *
     * @param int|string $accessId Access identifier for obtain the access model
     * @return AccessHandler
     */
    public function getAccess(int|string $accessId): AccessHandler;
}