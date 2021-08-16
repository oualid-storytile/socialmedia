<?php

namespace Storytile\Socialmedia\OAuthOne;

use InvalidArgumentException;

class TwitterProvider extends AbstractProvider
{
    const TYPE = "twitter";
    const TWITTER_API_URL = "https://api.twitter.com";
    const TWITTER_API_VERSION = "1.1";
    const TWITTER_ENDPOINT_USERS_SEARCH = "/users/search.json";
    const TWITTER_ENDPOINT_TWEETS_SEARCH = "/search/tweets.json";

    /**
     * Search Tweets/Users.
     * Use the const variables to define the endpoint.
     *
     * @param $token
     * @param $tokenSecret
     * @param $parameters  "q" is mandatory ['q' => 'nasa', 'result_type' => 'popular', 'count' => "100"]
     * @return array
     * @throws InvalidArgumentException
     */
    public function search($endpoint, $token, $tokenSecret, $parameters)
    {
        if ( ! isset($parameters["q"])) {
            throw new InvalidArgumentException("Missing endpoint parameters key ['q'].");
        }

        $tokenCredentials = $this->server->createClientCredentialsObject($token, $tokenSecret);

        //$uri = 'https://api.twitter.com/1.1/search/tweets.json'
        $uri = self::TWITTER_API_URL.'/'.self::TWITTER_API_VERSION.'/'.$endpoint;
        $url = $uri.'?'.http_build_query($parameters, '', '&');

        $client = $this->server->createHttpClient();

        $headers = $this->server->getHeaders($tokenCredentials, "GET", $uri, $parameters);

        $response = $client->get($url, [
            'headers' => $headers,
            'http_errors' => false,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function social()
    {
        if (! $this->hasNecessaryVerifier()) {
            throw new MissingVerifierException('Invalid request. Missing OAuth verifier.');
        }

        $user = $this->server->getUserDetails($token = $this->getToken(), $this->shouldBypassCache($token->getIdentifier(), $token->getSecret()));

        $extraDetails = [
            'location' => $user->location,
            'description' => $user->description,
        ];

        return (new Social)->setRaw(array_merge($user->extra, $user->urls, $extraDetails))
                ->setToken($token->getIdentifier(), $token->getSecret())
                ->setType(self::TYPE);
    }
}
