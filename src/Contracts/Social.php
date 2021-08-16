<?php

namespace Storytile\Socialmedia\Contracts;

interface Social
{
    /**
     * Get the actual token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Get the type of social media e.g: facebook, instagram, twitter.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the extra data witch need apis for request.
     *
     * @return array
     */
    public function getSocialData();
}
