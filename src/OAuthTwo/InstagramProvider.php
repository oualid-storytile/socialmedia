<?php

namespace Storytile\Socialmedia\OAuthTwo;

class InstagramProvider extends Facebook
{
    const TYPE = "instagram";
    /**
     * The media type being requested.
     *
     * @var string
     */
    protected $media = "top_media";

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ["public_profile", "instagram_basic", "pages_show_list"];

    /**
     * Set media type to request from Facebook.
     *
     * @param  string  $media
     * @return $this
     */
    public function media(string $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get user Account info by token.
     *
     * @param string $accessToken
     *
     * @return array
     */
    public function getFbAccountByToken($accessToken)
    {
        $this->lastToken = $accessToken;
        $accountUrl = $this->graphUrl.'/'.$this->version.'/me/accounts?access_token='.$accessToken;
        $response = $this->getHttpClient()->get($accountUrl, ['http_errors' => false]);

        $data = json_decode($response->getBody(), true);

        if ($this->isError($data)) {
            throw new FacebookGraphException('Facebook account was not returned from Graph.', 401);
        }

        return $data;
    }

    /**
     * Get user Account info by token.
     *
     * @param string $facebookId
     *
     * @return array
     */
    protected function getIgAccountByFbId($facebookId)
    {
        $accountUrl = $this->graphUrl.'/'.$this->version.'/'.$facebookId.'?fields=instagram_business_account&access_token='.$this->lastToken;
        $response = $this->getHttpClient()->get($accountUrl, ['http_errors' => false]);

        $data = json_decode($response->getBody(), true);

        if ($this->isError($data)) {
            throw new FacebookGraphException('Instagram account was not returned from Graph.', 401);
        }

        return $data;
    }

    /**
     * Get Instagram Hashtag id.
     *
     * @param string $hashtag
     * @param string $igBusinessAccountId
     * @param string $accessToken
     *
     * @return array
     */
    protected function getIgHashtagId($hashtag, $igBusinessAccountId, $accessToken)
    {
        $hashtagUrl = $this->graphUrl.'/'.$this->version.'/ig_hashtag_search?user_id='.$igBusinessAccountId.'&q='.$hashtag.'&fields=id,name&access_token='.$accessToken;
        $response = $this->getHttpClient()->get($hashtagUrl, ['http_errors' => false]);

        $data = json_decode($response->getBody(), true);

        if ($this->isError($data)) {
            throw new FacebookGraphException('Hashtag id was not returned from Graph.', 401);
        }

        return $data;
    }

    /**
     * Search Top/recent media by hashtag id.
     * Use @method fields() to fetch more data.
     * Use @method media($recentMedia) to set $recentMedia="recent_media".
     *
     * @param  string  $hashtagName
     * @param  string  $accessToken
     * @param  string  $igBusinessAccountId
     * @param  int     $limit
     * @param  null|string  $after
     *
     * @return array
     * @throws FacebookGraphException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchInstagramMedia($hashtagName, $accessToken, $igBusinessAccountId, $limit = 5, $after= null)
    {
        $hashtagData = $this->getIgHashtagId($hashtagName, $igBusinessAccountId, $accessToken);

        //Fields e.g: ["id","caption","children","comment_count","like_count","media_type","media_url","permalink"]
        $hashtagUrl = $this->graphUrl.'/'.$this->version.'/'. $hashtagData["data"][0]["id"].'/'.$this->media.'?user_id='.$igBusinessAccountId.'&fields='.implode(',', $this->fields).'&access_token='.$accessToken.'&limit='.$limit;
        if ($after) {
            $hashtagUrl .= '&after='.$after;
        }
        $response = $this->getHttpClient()->get($hashtagUrl, ['http_errors' => false]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSocialData($accessToken)
    {
        $this->lastToken = $accessToken;
        $facebookAccountData = $this->getFbAccountByToken($accessToken);
        $igAccountData = $this->getIgAccountByFbId($facebookAccountData["data"][0]["id"]);

        return [
            "ig_account_id" => $facebookAccountData["data"][0]["id"],
            "ig_business_account_id" => $igAccountData["instagram_business_account"]["id"]
        ];
    }
}
