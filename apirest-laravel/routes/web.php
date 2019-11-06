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

// Charge class
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

// Router of users
Route::post('api/user/register', 'UserController@register');
Route::post('api/user/login', 'UserController@login');
Route::put('api/user/update', 'UserController@update');
Route::post('api/user/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}', 'UserController@getImage');
Route::get('api/user/detail/{id}', 'UserController@detail');

// Router of categories
Route::resource('api/categories', 'CategoriesController');