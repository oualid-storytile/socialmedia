<?php

namespace Storytile\Socialmedia\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $provider
     * @return \Storytile\Socialmedia\Contracts\Provider
     */
    public function driver($provider = null);
}
