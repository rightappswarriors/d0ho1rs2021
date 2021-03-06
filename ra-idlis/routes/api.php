<?php

use Illuminate\Http\Request;
use App\Http\Middleware\APIMiddleware;

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

Route::get(
    '/clients', 
    'Client\Api\ClientApiController@index'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/application/validate-name/',
    'Client\Api\ApplicationApiController@check'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/application/save',
    'Client\Api\ApplicationApiController@save'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/application/fetch',
    'Client\Api\ApplicationApiController@fetch'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/province/fetch/',
    'Client\Api\ProvinceApiController@fetch'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/municipality/fetch/',
    'Client\Api\MunicipalityApiController@fetch'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/barangay/fetch/',
    'Client\Api\BarangayApiController@fetch'
); //->middleware([APIMiddleware::class]);

Route::post(
    '/classification/fetch/',
    'Client\Api\ClassificationApiController@fetch'
); //->middleware([APIMiddleware::class]);