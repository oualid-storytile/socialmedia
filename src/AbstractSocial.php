<?php

namespace Storytile\Socialmedia;

use ArrayAccess;
use Storytile\Socialmedia\Contracts\Social;

Abstract class AbstractSocial implements ArrayAccess, Social
{
    /**
     * The token for the user.
     *
     * @var mixed
     */
    public $token;

    /**
     * The social media type.
     *
     * @var mixed
     */
    public $type;

    /**
     * The extra social media data used by api requests.
     *
     * @var array
     */
    public $socialData;

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the type of social media e.g: facebook, instagram, twitter.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the extra data witch need apis for request.
     *
     * @return array
     */
    public function getSocialData()
    {
        return $this->socialData;
    }

    /**
     * Set the raw user array from the provider.
     *
     * @param  array  $socialData
     * @return $this
     */
    public function setSocialData(array $socialData)
    {
        $this->socialData = $socialData;

        return $this;
    }

    /**
     * Get the raw user array.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->socialData;
    }

    /**
     * Set the raw user array from the provider.
     *
     * @param  array  $socialData
     * @return $this
     */
    public function setRaw(array $socialData)
    {
        $this->socialData = $socialData;

        return $this;
    }

    /**
     * Map the given array onto the user's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw user attribute exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->socialData);
    }

    /**
     * Get the given key from the raw user.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->socialData[$offset];
    }

    /**
     * Set the given attribute on the raw user array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->socialData[$offset] = $value;
    }

    /**
     * Unset the given value from the raw user array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->socialData[$offset]);
    }

}
