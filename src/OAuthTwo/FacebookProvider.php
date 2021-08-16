<?php

namespace Storytile\Socialmedia\OAuthTwo;

class FacebookProvider extends Facebook
{
    const TYPE = "facebook";

    const FACEBOOK_ENDPOINT_ME = "/me";
    const FACEBOOK_ENDPOINT_PAGES_SEARCH = "/pages/search";
    const FACEBOOK_ENDPOINT_POSTS_SEARCH = "/posts";

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $fields = ['name', 'email', 'gender', 'verified', 'link'];

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ["pages_read_engagement", "pages_read_engagement", "pages_read_user_content"];

    /**
     * {@inheritdoc}
     */
    protected function getSocialData($accessToken)
    {
        //Fields e.g: ["name", "email", "gender", "verified", "link"]
        $meUrl = $this->graphUrl.'/'.$this->version.self::FACEBOOK_ENDPOINT_ME.'?access_token='.$accessToken.'&fields='.implode(',', $this->fields);

        if (! empty($this->clientSecret)) {
            $appSecretProof = hash_hmac('sha256', $accessToken, $this->clientSecret);

            $meUrl .= '&appsecret_proof='.$appSecretProof;
        }

        $response = $this->getHttpClient()->get($meUrl, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }



    /**
     * Search Facebook pages with key word.
     * Use @method fields() to fetch more data
     *
     * @param  string  $keyWord
     * @param  string  $accessToken
     *
     * @return array
     * @throws FacebookGraphException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchPages($keyWord, $accessToken)
    {
        //Fields e.g: ["id","caption","children","comment_count","like_count","media_type","media_url","permalink"]
        $hashtagUrl = $this->graphUrl.'/'.$this->version.self::FACEBOOK_ENDPOINT_PAGES_SEARCH.'?q='.$keyWord.'&fields='.implode(',', $this->fields).'&access_token='.$accessToken;

        $response = $this->getHttpClient()->get($hashtagUrl, ['http_errors' => false]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Search Top/recent media by hashtag id.
     * Use @method fields() to fetch more data
     *
     * @param  string  $pageId
     * @param  string  $accessToken
     *
     * @return array
     * @throws FacebookGraphException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchPagesPost($pageId, $accessToken)
    {
        //Fields e.g: ["id","caption","children","comment_count","like_count","media_type","media_url","permalink"]
        $hashtagUrl = $this->graphUrl.'/'.$this->version.'/'.$pageId.'/'.self::FACEBOOK_ENDPOINT_POSTS_SEARCH.'?fields='.implode(',', $this->fields).'&access_token='.$accessToken;

        $response = $this->getHttpClient()->get($hashtagUrl, ['http_errors' => false]);

        return json_decode($response->getBody(), true);
    }
}
