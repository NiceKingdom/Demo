<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('outside');
});

// 通用
Route::get ('/index', 'Common\UserController@getStatus');
Route::post('/register', 'Common\UserController@register');
Route::post('/login', 'Common\UserController@login')->middleware('resource.auto');
Route::get ('/logout', 'Common\UserController@logout');

// 领地
Route::get ('/user/get-resource', 'Common\ResourceController@getMeResource')->middleware('resource.auto');

// 建筑
Route::get ('/building/index', 'Building\BuildingController@index')->middleware('resource.auto');
Route::get ('/building/list', 'Building\BuildingController@buildingList');
Route::get ('/building/schedule', 'Building\BuildingController@schedule')->middleware('resource.auto');

Route::post('/building/build', 'Building\BuildingController@build')->middleware('resource.auto');
Route::post('/building/destroy', 'Building\BuildingController@destroy')->middleware('resource.auto');
Route::get ('/building/recall/{name}', 'Building\BuildingController@recall')->middleware('resource.auto');

// 初始化
Route::get ('/reset/redis', 'Common\InitializeController@resetRedis');
