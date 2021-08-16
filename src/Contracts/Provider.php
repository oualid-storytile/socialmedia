<?php

namespace Storytile\Socialmedia\Contracts;

interface Provider
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

    /**
     * Get the Social instance for the authenticated user.
     * the instance contains information that will be used to pass request on user's behalf.
     *
     * @return \Storytile\Socialmedia\Contracts\Social
     */
    public function social();
}
