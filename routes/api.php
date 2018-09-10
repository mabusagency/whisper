<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'cors'], function () {
    Route::get('/lp/visitor', 'ApiController@visitor');
    Route::post('/lp/submit', 'ApiController@submit');
    Route::post('/lp/link', 'ApiController@link');
    Route::post('/lp/options', 'ApiController@options');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
