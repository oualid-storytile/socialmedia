<?php

namespace Storytile\Socialmedia\OAuthTwo;;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

    /**
     * Get the Social instance for the authenticated user.
     *
     * @return \Storytile\Socialmedia\OAuthTwo\Social
     */
    public function social();
}
