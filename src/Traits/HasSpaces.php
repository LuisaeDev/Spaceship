<?php

namespace LuisaeDev\Spaceship\Traits;

use Illuminate\Support\Collection;
use LuisaeDev\Spaceship\AccessHandler;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship;
use LuisaeDev\Spaceship\Models\SpaceshipAccess;
use LuisaeDev\Spaceship\RoleHandler;
use LuisaeDev\Spaceship\SpaceHandler;

trait HasSpaces
{
    public function accesses()
    {
        return $this->hasMany(SpaceshipAccess::class);
    }

    /**
     * Check if the current user has access to a spacific space.
     *
     * @param  SpaceHandler|int|string  $space
     * @return bool
     */
    public function hasAccess(SpaceHandler|int|string $space): bool
    {
        try {

            // Define the space handler instance
            if ((is_string($space)) || (is_int($space))) {
                $space = Spaceship::getSpace($space);
            }

            return $space->hasAccess($this);
        } catch (SpaceshipException $e) {
            return false;
        }
    }

    /**
     * Check if the current user can access to a specific space.
     *
     * @param  SpaceHandler|int|string  $space
     * @return bool
     */
    public function canAccess(SpaceHandler|int|string $space): bool
    {
        try {

            // Define the space handler instance
            if ((is_string($space)) || (is_int($space))) {
                $space = Spaceship::getSpace($space);
            }

            return $space->canAccess($this);
        } catch (SpaceshipException $e) {
            return false;
        }
    }

    /**
     * Check if the current user can access to any of multiple specificied spaces.
     *
     * @param  SpaceHandler|int|string  ...$spaces
     * @return bool
     */
    public function canAccessAny(SpaceHandler|int|string ...$spaces): bool
    {
        foreach ($spaces as $space) {
            if ($this->canAccess($space)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the current user can access to all of multiple specificied spaces.
     *
     * @param  SpaceHandler|int|string  ...$spaces
     * @return bool
     */
    public function canAccessAll(SpaceHandler|int|string ...$spaces): bool
    {
        foreach ($spaces as $space) {
            if (! $this->canAccess($space)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the current user can not access to all of multiple specificied spaces.
     *
     * @param  SpaceHandler|int|string  ...$spaces
     * @return bool
     */
    public function unlessAccess(SpaceHandler|int|string ...$spaces): bool
    {
        foreach ($spaces as $space) {
            if ($this->canAccess($space)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return a collection with all accesses models related to the current user.
     *
     * @return Collection
     */
    public function getAccesses(): Collection
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return collect([]);
        }

        return $this->accesses->load(['space', 'role']);
    }

    /**
     * For the current user, return the access related to the specific space.
     *
     * @param  SpaceHandler|int|string  $space
     * @return AccessHandler|null
     */
    public function accessFrom(SpaceHandler|int|string $space): ?AccessHandler
    {
        try {

            // Define the space handler instance
            if ((is_string($space)) || (is_int($space))) {
                $space = Spaceship::getSpace($space);
            }
        } catch (SpaceshipException $e) {
            return null;
        }

        return $space->getAccess($this);
    }

    /**
     * For the current user, return the role related to the specific space.
     *
     * @param  SpaceHandler|int|string  $space
     * @return RoleHandler|null
     */
    public function roleFrom(SpaceHandler|int|string $space): ?RoleHandler
    {
        try {

            // Define the space handler instance
            if ((is_string($space)) || (is_int($space))) {
                $space = Spaceship::getSpace($space);
            }
        } catch (SpaceshipException $e) {
            return null;
        }

        return $space->getRole($this);
    }
}
