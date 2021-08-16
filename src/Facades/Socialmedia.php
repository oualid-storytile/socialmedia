<?php

namespace Storytile\Socialmedia\Facades;

use Illuminate\Support\Facades\Facade;
use Storytile\Socialmedia\Contracts\Factory;

/**
 * @method static \Storytile\Socialmedia\Contracts\Provider driver(string $provider = null)
 * @see \Storytile\Socialmedia\SocialmediaManager
 */
class Socialmedia extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
