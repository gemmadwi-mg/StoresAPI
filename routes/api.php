<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductLineController;
use App\Http\Controllers\StoreController;
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

    $api->get('categories', 'App\Http\Controllers\CategoryController@index');
    $api->get('categories/{id}', 'App\Http\Controllers\CategoryController@show');

    $api->post('/signup', 'App\Http\Controllers\UserController@store');

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
        $api->resource('users', AdminUserController::class);
        $api->post('users/{id}/suspend', 'App\Http\Controllers\Admin\AdminUserController@suspend');
        $api->post('users/{id}/activate', 'App\Http\Controllers\Admin\AdminUserController@activate');
        $api->get('users/{id}/roles', 'App\Http\Controllers\Admin\AdminRolesController@show');
        $api->get('users/{id}/permissions', 'App\Http\Controllers\Admin\AdminPermissionsController@show');
        $api->post('users/{id}/roles', 'App\Http\Controllers\Admin\AdminRolesController@changeRole');
        $api->post('products/categories', 'App\Http\Controllers\CategoryController@store');
        $api->put('products/categories/{id}', 'App\Http\Controllers\CategoryController@update');
        $api->delete('products/categories/{id}', 'App\Http\Controllers\CategoryController@destroy');
    });

    $api->group(['middleware' => ['role:store-owner'], 'prefix' => 'owner'], function ($api) {
        $api->post('stores', 'App\Http\Controllers\StoreController@store');
        $api->group(['middleware' => 'isStoreOwner'], function ($api) {
            $api->resource('stores', StoreController::class, ['except' => ['store']]);
            $api->resource('stores/{store}/brands', BrandController::class);
            $api->resource('stores/{store}/brands/{brands}/productlines', ProductLineController::class);
        });
    });
});
