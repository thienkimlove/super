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
Route::get('admin/cron', 'Backend\HomeController@cron');
Route::get('admin/thongke', 'Backend\HomeController@thongke');
Route::get('admin/clearlead', 'Backend\HomeController@clearlead');
Route::get('admin/statistic/{content}', 'Backend\HomeController@statistic');
Route::get('admin/ajax/{content}', 'Backend\HomeController@ajax');
Route::get('admin/recent-lead', 'Backend\HomeController@ajaxSiteRecentLead');
Route::get('admin/offertest/{id}', 'Backend\HomeController@submit');
Route::resource('admin/users', 'Backend\UsersController');
Route::resource('admin/offers', 'Backend\OffersController');
Route::resource('admin/groups', 'Backend\GroupsController');
Route::resource('admin/networks', 'Backend\NetworksController');

#Frontend Routes
Route::get('/', 'Frontend\MainController@index');
Route::get('camp', 'Frontend\MainController@camp');
Route::get('check', 'Frontend\MainController@check');
Route::get('postback', 'Frontend\MainController@postback');
Route::get('hashpostback', 'Frontend\MainController@hashpostback');
Route::post('postback', 'Frontend\MainController@postback');
Route::get('xmedia', 'Frontend\MainController@xmedia');

Route::get('test_ip', function(){
   $ipInformation = file_get_contents('http://freegeoip.net/json/1.52.80.71');
   $address = json_decode($ipInformation, true);
   echo $address['country_code'];
});

