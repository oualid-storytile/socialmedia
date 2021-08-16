<?php

namespace Storytile\Socialmedia\OAuthOne\Client\Server;

use App\Social;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Auth;
use Storytile\Socialmedia\OAuthOne\Client\Credentials\ClientCredentials;
use Storytile\Socialmedia\OAuthOne\Client\Credentials\CredentialsException;
use Storytile\Socialmedia\OAuthOne\Client\Credentials\TemporaryCredentials;
use Storytile\Socialmedia\OAuthOne\Client\Credentials\TokenCredentials;

class Twitter extends Server
{
    /**
     * @inheritDoc
     */
    public function urlTemporaryCredentials()
    {
        return 'https://api.twitter.com/oauth/request_token';
    }

    /**
     * @inheritDoc
     */
    public function urlAuthorization()
    {
        return 'https://api.twitter.com/oauth/authenticate';
    }

    /**
     * @inheritDoc
     */
    public function urlTokenCredentials()
    {
        return 'https://api.twitter.com/oauth/access_token';
    }

    /**
     * @inheritDoc
     */
    public function urlUserDetails()
    {
        return 'https://api.twitter.com/1.1/account/verify_credentials.json?include_email=true';
    }


    /**
     * Gets temporary credentials by performing a request to
     * the server.
     *
     * @var $tokenCredentials
     * @return ClientCredentials
     */
    public function createClientCredentialsObject($token, $tokenSecret)
    {
        return $this->createClientCredentials([
            'identifier' => $token,
            'secret' => $tokenSecret,
        ]);
    }


    /**
     * @inheritDoc
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $user = new User();

        $user->uid = $data['id_str'];
        $user->nickname = $data['screen_name'];
        $user->name = $data['name'];
        $user->location = $data['location'];
        $user->description = $data['description'];
        $user->imageUrl = $data['profile_image_url'];

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        $used = ['id', 'screen_name', 'name', 'location', 'description', 'profile_image_url', 'email'];

        foreach ($data as $key => $value) {
            if (strpos($key, 'url') !== false) {
                if ( ! in_array($key, $used)) {
                    $used[] = $key;
                }

                $user->urls[$key] = $value;
            }
        }

        // Save all extra data
        $user->extra = array_diff_key($data, array_flip($used));

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['id'];
    }

    /**
     * @inheritDoc
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['name'];
    }
}
