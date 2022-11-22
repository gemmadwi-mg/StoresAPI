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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) {
    $api->get('hello', function () {
        return 'Hello Stores API';
    });

    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('/signup', 'App\Http\Controllers\UserController@store');
        $api->post('/login', 'App\Http\Controllers\Auth\AuthController@login');
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            $api->post('/token/refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
            $api->post('/logout', 'App\Http\Controllers\Auth\AuthController@logout');
        });
    });

    $api->group(['prefix' => 'me', 'middleware' => 'jwt.auth'], function ($api) {
        $api->get('/profile', 'App\Http\Controllers\UserProfileController@index');
        $api->post('/profile', 'App\Http\Controllers\UserProfileController@store');
        $api->put('/profile', 'App\Http\Controllers\UserProfileController@update');
        $api->delete('/profile', 'App\Http\Controllers\UserProfileController@delete');
    });

    $api->group(['middleware' => ['role:super-admin'], 'prefix' => 'admin'], function ($api) {
        $api->get('/users', 'App\Http\Controllers\Admin\AdminUserController@index');
        
    });
});
