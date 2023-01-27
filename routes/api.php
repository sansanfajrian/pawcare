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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pawcare')->group(function() {
    Route::post('/login', 'Api\ApiController@login');
    Route::post('/register', 'Api\ApiController@registerUser');
    Route::post('/user/edit/{id}', 'Api\ApiController@editUser');
    Route::get('/doctor/list', 'Api\ApiController@doctorList');
    Route::get('/doctor/detail/{id}', 'Api\ApiController@doctorDetail');
    Route::get('/banner/list', 'Api\ApiController@bannerList');
    Route::post('/consultation', 'Api\ApiController@consultation');
    Route::get('/consultation/list', 'Api\ApiController@consultationList');
    Route::post('/payment', 'Api\ApiController@payment');
    Route::post('/review', 'Api\ApiController@review');
});
