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

Route::namespace('Api')->middleware('localization')->group(function () {
    Route::prefix('auth')->namespace('Auth')->group(function() {
        Route::post('login', 'LoginController@login');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'ResetPasswordController@reset');
        Route::middleware('auth:api')->post('logout', 'LoginController@logout');
    });
    Route::middleware('auth:api')->group(function() {
        Route::get('user', 'UserController@index');
        Route::put('change/password', 'UserController@change_password');
        Route::put('user/theme', 'UserController@theme');
        Route::get('home', 'HomeController@index');
        Route::apiResource('products', 'ProductsController')->except(['show']);
        Route::apiResource('products/categories', 'ProductsCategoriesController')->except(['show']);
        Route::apiResource('products/brands', 'ProductsBrandsController')->except(['show']);
        Route::apiResource('funds/product', 'FundsController')->except(['show']);
        Route::get('funds/product/export', 'FundsController@export');
        Route::post('funds/product/import', 'FundsController@import');
        Route::get('funds/years', 'FundsController@years');
        Route::apiResource('funds/network', 'FundsNetworkController')->except(['show']);
        Route::get('funds/network/export', 'FundsNetworkController@export');
        Route::post('funds/network/import', 'FundsNetworkController@import');
        Route::get('funds/network/filters', 'FundsNetworkController@filters');
        Route::get('products/filters', 'ProductsController@filters');
        Route::apiResource('networks', 'NetworksController')->except(['show']);
        Route::apiResource('actions/types', 'ActionTypesController')->except(['show']);
    });
});
