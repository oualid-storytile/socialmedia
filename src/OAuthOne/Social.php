<?php

namespace Storytile\Socialmedia\OAuthOne;

use Storytile\Socialmedia\AbstractSocial;

class Social extends AbstractSocial
{
    /**
     * The user's access token secret.
     *
     * @var string
     */
    public $tokenSecret;

    /**
     * Set the token on the user.
     *
     * @param  string  $token
     * @param  string  $tokenSecret
     * @return $this
     */
    public function setToken($token, $tokenSecret)
    {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        return $this;
    }

    /**
     * Set the type of social media e.g: twitter.
     *
     * @param  string  $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }
}
