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

    // Authentication
    // Route::group(['middleware' => 'auth:api'], function() {
    //     Route::get('/all', 'Api\v1\UserController@index');
    //     Route::post('/add', 'Api\v1\UserController@store');
    //     Route::get('/show/{id}', 'Api\v1\UserController@show');
    // });
});
