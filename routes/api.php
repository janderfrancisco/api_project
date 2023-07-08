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

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('auth', 'Auth\AuthApiController@authenticate');
Route::post('reset-password', 'Auth\PasswordResetRequestController@sendPasswordResetEmail');
Route::post('change-password', 'Auth\ChangePasswordController@passwordResetProcess');
Route::group([
    'middleware' => 'auth:api'
    ], function () {
        Route::get('authenticated-user', 'Auth\AuthApiController@getAuthenticatedUser');
        Route::post('auth-refresh', 'Auth\AuthApiController@refreshToken');
        Route::get('api-logout', 'Auth\AuthApiController@apiLogout');
    }
);

/*
|--------------------------------------------------------------------------
| Rotas autenticadas com JWT, também são acessadas com prefixo e pasta v1
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix'     => 'v1',
    'namespace'  => 'Api\v1',
    'middleware' => 'auth:api'
    ], function () {
        Route::apiResources([
            'user'           => 'UserController',
            'product'        => 'ProductController',
        ]);
});

