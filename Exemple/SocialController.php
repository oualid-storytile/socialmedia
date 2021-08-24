<?php

namespace App\Http\Controllers;

use App\Social;
use Illuminate\Support\Facades\Auth;
use Atymic\Twitter\Facade\Twitter;
use Storytile\Socialmedia\Facades\Socialmedia;
use Storytile\Socialmedia\OAuthOne\TwitterProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @return RedirectResponse
     */
    public function redirect(string $provider)
    {
        //$social = Social::where("user_id", Auth::user()->id)->where("type", "twitter")->first();
        return Socialmedia::driver($provider)->redirect();
    }

    /**
     * Obtain the credentials data information from Provider.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function callback(string $provider)
    {
        $social = Socialmedia::driver($provider)->social();

        //dd($social);

        $apiData = [];
        if ("instagram" === $provider) {
            $apiData = $social->getSocialData();
        } elseif ("twitter" === $provider) {
            $apiData["tokenSecret"] = $social->getTokenSecret();
        }

        Social::updateOrCreate([
            "type" => $social->getType(),
            "user_id" => Auth::user()->id],
            [
                "api_data" => $apiData,
                "token" => $social->getToken()
            ]);

        $facebook = Social::where("user_id", Auth::user()->id)->where("type", "facebook")->first();
        $instagram = Social::where("user_id", Auth::user()->id)->where("type", "instagram")->first();
        $twitter = Social::where("user_id", Auth::user()->id)->where("type", "twitter")->first();

        return view('home',[
            "facebook" => $facebook,
            "instagram" => $instagram,
            "twitter" => $twitter
        ]);
    }

    /**
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function search(string $provider, string $tag, string $after = null)
    {
        $social = Social::where("user_id", Auth::user()->id)->where("type", $provider)->first();
        $response = [];

        if ($provider === "instagram") {
            $response = Socialmedia::with($provider)
                ->fields(["id","caption","children","comment_count","like_count","media_type","media_url","permalink"])
                ->media("top_media")
                ->searchInstagramMedia($tag, $social->token, $social->api_data["ig_business_account_id"],5, $after);
        }

        if ($provider === "facebook") {

            $response = Socialmedia::with($provider)
                ->fields(["id","name","location","link"])
                ->searchPages($tag, $social->token);

            dd($response);
        }

        if ($provider === "twitter") {

            // Get Users
            $response =  Socialmedia::with($provider)->search(
                TwitterProvider::TWITTER_ENDPOINT_USERS_SEARCH,
                $social->token,
                $social->api_data["tokenSecret"], [
                    'q' => $tag,
                    'result_type' => 'popular',
                    'count' => "100"]
            );

            // Get Tweets
            /*$response =  Socialmedia::with($provider)->search(
                TwitterProvider::TWITTER_ENDPOINT_TWEETS_SEARCH,
                $social->token,
                $social->api_data["tokenSecret"], [
                    'q' => $tag,
                    'result_type' => 'popular',
                    'count' => "100"]
            );*/
            dd($response);
        }
        //dd($response);
        return view('instagram-show',[
            "hashtagName" => $tag,
            "media" => $response["data"],
            "after" => $response["paging"]["cursors"]["after"],
        ]);
    }

}
