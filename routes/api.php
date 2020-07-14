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


Route::group([
    'prefix' => 'social',
], function () {
    Route::post("get-profile", 'SocialProfileController@getProfile');
    Route::post("edit-profile", 'SocialProfileController@editProfile');
    Route::post("add-connection", 'SocialProfileController@addConnection');
    Route::post("accept-connection-request", 'SocialProfileController@acceptConnectionRequest');
    Route::post("get-connections", 'SocialProfileController@getConnections');
    Route::get("get-suggested-connections", 'SocialProfileController@getSuggestedConnections');
});

