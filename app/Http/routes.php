<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Authentication
 */
Route::group(['prefix' => 'auth'], function () {
    
    Route::get('login', [
        'as' => 'authGetLogin', 'uses' => 'Auth\AuthController@getLogin'
    ]);
    Route::post('login', [
        'as' => 'authPostLogin', 'uses' => 'Auth\AuthController@postLogin'
    ]);
    Route::get('logout', [
        'as' => 'authLogout', 'uses' => 'Auth\AuthController@getLogout'
    ]);
});

/**
 * Root
 */
Route::get('/', ['as' => 'root', function () {
        return view('welcome');
}]);

/**
 * Laravel default home redirection to admin
 */
Route::get('home', ['as' => 'home', function () {
        return Redirect::route('adminDashboard');
}]);

/**
 * Admin
 */
Route::group(['prefix' => env('APP_ADMIN_URL'), 'middleware' => 'auth'], function () {
    
    /**
     * Main dashboard
     */
    Route::get('/', [
        'as' => 'adminDashboard', 'uses' => 'Admin\DashboardController@index'
    ]);
});