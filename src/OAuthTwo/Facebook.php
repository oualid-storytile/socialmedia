<?php

namespace Storytile\Socialmedia\OAuthTwo;

use Illuminate\Support\Arr;

Abstract class Facebook extends AbstractProvider implements ProviderInterface
{
    /**
     * The base Facebook Graph URL.
     *
     * @var string
     */
    protected $graphUrl = 'https://graph.facebook.com';

    /**
     * The Graph API version for the request.
     *
     * @var string
     */
    protected $version = 'v3.3';

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Display the dialog in a popup view.
     *
     * @var bool
     */
    protected $popup = false;

    /**
     * Re-request a declined permission.
     *
     * @var bool
     */
    protected $reRequest = false;

    /**
     * The access token that was last used to retrieve a user.
     *
     * @var string|null
     */
    protected $lastToken;

    /**
     * Set the user fields to request from Facebook.
     *
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set the dialog to be displayed as a popup.
     *
     * @return $this
     */
    public function asPopup()
    {
        $this->popup = true;

        return $this;
    }

    /**
     * Re-request permissions which were previously declined.
     *
     * @return $this
     */
    public function reRequest()
    {
        $this->reRequest = true;

        return $this;
    }

    /**
     * Get the last access token used.
     *
     * @return string|null
     */
    public function lastToken()
    {
        return $this->lastToken;
    }

    /**
     * Specify which graph version should be used.
     *
     * @param  string  $version
     * @return $this
     */
    public function usingGraphVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['access_token'])) {
            throw new FacebookGraphException('Access token was not returned from Graph.', 401);
        }

        if (isset($data["expires_in"]) && $data["expires_in"] <= (60 * 60 * 24 * 59)) {
            $data = $this->getLongLivedAccessToken($data["access_token"]);
        }

        $data['type'] = static::TYPE;

        return Arr::add($data, 'expires_in', Arr::pull($data, 'expires'));
    }

    /**
     * Exchanges a short-lived access token with a long-lived access token.
     *
     * @param string $accessToken
     *
     * @return array
     */
    public function getLongLivedAccessToken($accessToken)
    {
        $longLiveTokenUrl = $this->getTokenUrl().'?'.http_build_query($this->getLongLiveTokenFields($accessToken), '', '&');
        $response = $this->getHttpClient()->get($longLiveTokenUrl);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.facebook.com/'.$this->version.'/dialog/oauth', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->graphUrl.'/'.$this->version.'/oauth/access_token';
    }

    /**
     * Returns true if Graph returned an error message.
     *
     * @return boolean
     */
    protected function isError($response)
    {
        return isset($response['error']);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapSocialToObject(array $socialData)
    {
        return (new Social)->setRaw($socialData);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        if ($this->popup) {
            $fields['display'] = 'popup';
        }

        if ($this->reRequest) {
            $fields['auth_type'] = 'rerequest';
        }

        return $fields;
    }

    /**
     * Debug token.
     *
     * @param string $accessToken
     *
     * @return array
     */
    protected function debugToken($accessToken)
    {
        $urlDebug = $this->graphUrl.'/'.$this->version."/debug_token?input_token={$accessToken}&access_token={$accessToken}";
        $response = $this->getHttpClient()->get($urlDebug);

        $data = json_decode($response->getBody(), true);

        $data["data"]["expires_at_in_days"] = $data["data"]["expires_at"] != 0 ? $data["data"]["expires_at_in_days"]/(60 * 60 * 24) : 90;

        return $data;
    }

    protected abstract function getSocialData($token);
}
