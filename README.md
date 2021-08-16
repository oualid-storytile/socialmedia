
## Introduction

Storytile socialmedia provides an interface to OAuth authentication and search with Facebook, Instagram and Twitter.

You need to create an application (Facebook, Twitter) and create your credentials in

## Installation

```
composer require storytile-oualid/socialmedia
```

## Configuration

Add the file config/socialmedia to your laravel project configuration. 
Set the below environment variables in your `.env`.

```
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
FACEBOOK_REDIRECT_URL=

INSTAGRAM_REDIRECT_URL=

TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
```


Laravel Socialite is open-sourced software licensed under the [MIT license](LICENSE.md).
