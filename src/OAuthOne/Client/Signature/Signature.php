<?php

namespace Storytile\Socialmedia\OAuthOne\Client\Signature;

use Storytile\Socialmedia\OAuthOne\Client\Credentials\ClientCredentialsInterface;
use Storytile\Socialmedia\OAuthOne\Client\Credentials\CredentialsInterface;

abstract class Signature implements SignatureInterface
{
    /**
     * The client credentials.
     *
     * @var ClientCredentialsInterface
     */
    protected $clientCredentials;

    /**
     * The (temporary or token) credentials.
     *
     * @var CredentialsInterface
     */
    protected $credentials;

    /**
     * @inheritDoc
     */
    public function __construct(ClientCredentialsInterface $clientCredentials)
    {
        $this->clientCredentials = $clientCredentials;
    }

    /**
     * @inheritDoc
     */
    public function setCredentials(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Generate a signing key.
     *
     * @return string
     */
    protected function key()
    {
        $key = rawurlencode($this->clientCredentials->getSecret()) . '&';

        if ($this->credentials !== null) {
            $key .= rawurlencode($this->credentials->getSecret());
        }

        return $key;
    }
}
