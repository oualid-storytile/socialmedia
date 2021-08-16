<?php

namespace Storytile\Socialmedia;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Storytile\Socialmedia\OAuthOne\TwitterProvider;
use Storytile\Socialmedia\OAuthTwo\FacebookProvider;
use Storytile\Socialmedia\OAuthTwo\InstagramProvider;
use Storytile\Socialmedia\OAuthOne\Client\Server\Twitter as TwitterServer;

class SocialmediaManager extends Manager implements Contracts\Factory
{
    /**
     * Get a provider instance.
     *
     * @param  string  $provider
     * @return mixed
     */
    public function with($provider)
    {
        return $this->driver($provider);
    }

    /**
     * Create an instance of the specified provider.
     *
     * @return \Storytile\Socialmedia\OAuthTwo\AbstractProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->config->get('socialmedia.facebook');

        return $this->buildProvider(
            FacebookProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified provider.
     *
     * @return \Storytile\Socialmedia\OAuthTwo\AbstractProvider
     */
    protected function createInstagramDriver()
    {
        $config = $this->config->get('socialmedia.instagram');

        return $this->buildProvider(
            InstagramProvider::class, $config
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Storytile\Socialmedia\OAuthTwo\AbstractProvider
     */
    public function buildProvider(string $provider, array $config)
    {
        return new $provider(
            $this->container->make('request'), $config['client_id'],
            $config['client_secret'], $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Storytile\Socialmedia\OAuthOne\AbstractProvider
     */
    protected function createTwitterDriver()
    {
        $config = $this->config->get('socialmedia.twitter');

        return new TwitterProvider(
            $this->container->make('request'), new TwitterServer($this->formatConfig($config))
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
            ? $this->container->make('url')->to($redirect)
            : $redirect;
    }

    /**
     * Remove all the resolved provider instances.
     *
     * @return $this
     */
    public function removeProviders()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->app = $container;
        $this->container = $container;

        return $this;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Provider was specified as Default.');
    }
}
