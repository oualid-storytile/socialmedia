<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Provider can have only one of the values: twitter, facebook, instagram
Route::get('/{provider}/login/', 'SocialLoginController@redirect')->name('provider.login');
Route::get('/{provider}/redirect', 'SocialLoginController@callback');

// tag(mandatory) has the value of the word we are searching
// after(optional) will be used for link to get next page
Route::get('/{provider}/search/{tag}/{type?}/{after?}', 'SocialLoginController@search')->name('search');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
