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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Users Routes
Route::prefix('/user')->group(function() {
    Route::post('/register', 'Api\v1\UserController@registerUser');
    Route::post('/login', 'Api\v1\UserController@userLogin');
    Route::post('/create/role', 'Api\v1\UserController@createRole');
    Route::get('/assign/role/{name}/{id}', 'Api\v1\UserController@giveRole');
    Route::get('/check/role/{id}', 'Api\v1\UserController@checkRole');

    // Authentication
    /*
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/all', 'Api\v1\UserController@index')->middleware('scope:admin');
        Route::get('/show/{id}', 'Api\v1\UserController@show')->middleware('scope:admin,customer');
    }); */

    Route::get('/all', 'Api\v1\UserController@index')->middleware(['auth:api', 'scope:admin']);
    Route::get('/show/{id}', 'Api\v1\UserController@show')->middleware(['auth:api', 'scope:admin,customer']);
});
