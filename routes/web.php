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
Route::resource('admin/posts', 'Backend\PostsController');
Route::resource('admin/categories', 'Backend\CategoriesController');

#Frontend Routes
Route::get('/', 'Frontend\MainController@index');

