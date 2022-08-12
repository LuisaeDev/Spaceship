<?php

namespace LuisaeDev\Spaceship\Traits;

use LuisaeDev\Spaceship\AccessHandler;
use LuisaeDev\Spaceship\Facades\Spaceship;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\SpaceHandler;
use LuisaeDev\Spaceship\RoleHandler;

trait HasSpaces {

   /**
    * Check if the current user has access to a spacific space.
    *
    * @param SpaceHandler|string|null $space
    * @return boolean
    */
   public function hasAccess(SpaceHandler|string|null $space = null): bool
   {

      try {

         // Define the space handler instance
         if (is_null($space)) {
            $space = config('spaceship.default-space');
         }
         if (is_string($space)) {
            $space = Spaceship::getSpace($space);
         }

         return $space->hasAccess($this);

      }  catch (SpaceshipException $e) {
         return false;
      }
   }

   /**
    * Check if the current user can access to a specific space.
    *
    * @param SpaceHandler|string|null $space
    * @return boolean
    */
   public function canAccess(SpaceHandler|string|null $space = null): bool
   {

      try {

         // Define the space handler instance
         if (is_null($space)) {
            $space = config('spaceship.default-space');
         }
         if (is_string($space)) {
            $space = Spaceship::getSpace($space);
         }

         return $space->canAccess($this);

      }  catch (SpaceshipException $e) {
         return false;
      }
   }

   /**
    * For the current user, return the access related to the specific space.
    *
    * @param SpaceHandler|string|null $space
    * @return AccessHandler|null
    */
   public function getAccess(SpaceHandler|string|null $space = null): ?AccessHandler
   {

      // Define the space handler instance
      if (is_null($space)) {
         $space = config('spaceship.default-space');
      }
      if (is_string($space)) {
         $space = Spaceship::getSpace($space);
      }

      return $space->getAccess($this);
   }

   /**
    * For the current user, return the role related to the specific space.
    *
    * @param SpaceHandler|string|null $space
    * @return RoleHandler|null
    */
   public function getRole(SpaceHandler|string|null $space = null): ?RoleHandler
   {

      // Define the space handler instance
      if (is_null($space)) {
         $space = config('spaceship.default-space');
      }
      if (is_string($space)) {
         $space = Spaceship::getSpace($space);
      }

      return $space->getRole($this);
   }
}