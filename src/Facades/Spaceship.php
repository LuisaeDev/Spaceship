<?php

namespace LuisaeDev\Spaceship\Facades;

use Illuminate\Support\Facades\Facade;

class Spaceship extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \LuisaeDev\Spaceship\Spaceship::class;
    }
}
