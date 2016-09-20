<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

#Admin Routes
Route::get('admin/login', 'Backend\AuthController@redirectToGoogle');
Route::get('admin/logout', 'Backend\AuthController@logout');
Route::get('admin/callback', 'Backend\AuthController@handleGoogleCallback');


Route::get('admin', 'Backend\HomeController@index');
Route::get('admin/control', 'Backend\HomeController@control');
Route::resource('admin/users', 'Backend\UsersController');
Route::resource('admin/offers', 'Backend\OffersController');

#Frontend Routes
Route::get('/', 'Frontend\MainController@index');
Route::get('camp', 'Frontend\MainController@camp');

