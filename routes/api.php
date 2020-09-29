<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['middleware' => 'checktoken'], function(){ // Custom Token Auth middleware
    Route::group([
        'prefix' => 'social',
    ], function () {
        Route::post("get-profile", 'SocialProfileController@getProfile');
        Route::post("edit-profile", 'SocialProfileController@editProfile');
        Route::post("add-connection", 'SocialProfileController@addConnection');
        Route::post("accept-connection-request", 'SocialProfileController@acceptConnectionRequest');
        Route::post("get-connections", 'SocialProfileController@getConnections');
        Route::get("get-suggested-connections", 'SocialProfileController@getSuggestedConnections');
        Route::post("follow-connection", 'SocialProfileController@followConnection');
        Route::post("unfollow-connection", 'SocialProfileController@unFollowConnection');
    });

    Route::group([
        'prefix' => 'set',
    ], function () {
        Route::group([
            'namespace' => 'Posts',
        ], function () {
            Route::post("post", 'PostsController@create');
            Route::post("react", 'PostsController@react');
        });

    });


    Route::group([
        'prefix' => 'get',
    ], function () {
        Route::group([
            'namespace' => 'Posts',
        ], function () {
            Route::get("post", 'PostsController@posts');
            Route::get("post/{id}", 'PostsController@single');
            
        });

    });
// });
