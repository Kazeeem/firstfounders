<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/paystack', 'Api\v1\PaymentController@index');
Route::post('/pay', 'Api\v1\PaymentController@redirectToGateway')->name('pay');
Route::get('/tran', 'Api\v1\PaymentController@genTranxRef'); 
Route::get('/payment/callback', 'Api\v1\PaymentController@handleGatewayCallback');
