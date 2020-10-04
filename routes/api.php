<?php

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'v1',
    'namespace' => 'API',

], function ($router) {
//below routes are public, user can acces it without any restirction
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        //refresh jwt token
        Route::post('refresh', 'AuthController@refresh');
    });

//below routes are only available for the Authenticated users
    Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
        Route::post('logout', 'AuthController@logout');
        //get user info
        Route::get('user', 'AuthController@user');



    });

});
