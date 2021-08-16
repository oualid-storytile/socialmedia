
## Introduction

Storytile socialmedia provides an interface to OAuth authentication and search with Facebook, Instagram and Twitter.

You need to create an application (Facebook, Twitter) and create your credentials in

## Installation

```
composer config repositories.oualid-storytile/socialmedia vcs https://github.com/oualid-storytile/socialmedia
composer require oualid-storytile/socialmedia
composer require oualid-storytile/socialmedia
```

## Configuration
Add the file Database/migrations/2021_08_10_130951_create_user_socials_table.php to your migrations
Add the file config/socialmedia.php to your laravel project configuration. 
Set the below environment variables in your `.env`.

```
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
FACEBOOK_REDIRECT_URL=

INSTAGRAM_REDIRECT_URL=

TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
```

to get data take a look on Exemple/Socialcontroller.php


Laravel Socialite is open-sourced software licensed under the [MIT license](LICENSE.md).
