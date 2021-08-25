<?php

// Provider can have only one of the values: twitter, facebook, instagram
// Redirect users to the Social Media login page
Route::get('/{provider}/login/', 'SocialLoginController@redirect')->name('provider.login');
// Callback after login process is finished
Route::get('/{provider}/redirect', 'SocialLoginController@callback');

// tag(mandatory) has the value of the word we are searching
// type(mandatory and only for Twitter has the value "users" or "hashtag")
// instead of type use page id as parameter to get posts from facebook page
// after(optional) will be used for link to get next page
Route::get('/{provider}/search/{tag}/{type?}', 'SocialLoginController@search')->name('search');
