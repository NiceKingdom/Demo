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
Route::get ('/logout', 'Common\UserController@logout');

Route::middleware(['resource.auto', 'check'])->group(function () {
// 用户
    Route::post('/login', 'Common\UserController@login')->middleware('resource.auto');

// 领地
    Route::get ('/user/get-resource', 'Common\ResourceController@getMeResource');
    Route::post('/lord/policy/history', 'Common\ResourceController@getPolicyHistory');
    Route::post('/lord/policy/enlisting/open', 'Common\ResourceController@openEnlisting');
    Route::get ('/lord/policy/enlisting/stop/{x}/{y}', 'Common\ResourceController@stopEnlisting');
    Route::post('/lord/policy/deported/open', 'Common\ResourceController@openDeported');
    Route::get ('/lord/policy/deported/stop/{x}/{y}', 'Common\ResourceController@stopDeported');

// 建筑
    Route::get ('/building/index', 'Building\BuildingController@index');
    Route::get ('/building/schedule', 'Building\BuildingController@schedule');
    Route::post('/building/build', 'Building\BuildingController@build');
    Route::post('/building/destroy', 'Building\BuildingController@destroy');
    Route::get ('/building/recall/{name}', 'Building\BuildingController@recall');
    Route::get ('/building/list', 'Building\BuildingController@buildingList');
});

// 初始化
Route::get ('/reset/redis', 'Common\InitializeController@resetRedis');
