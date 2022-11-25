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

Route::group(['namespace' => '\App\Http\Controllers\Api'], function () {
    Route::post('/update-kinds', 'PlantController@updateKinds');
    Route::post('/update-plants', 'PlantController@updatePlants');
    Route::post('/remove-cart', 'PlantController@removeCart');
    Route::post('/save-location', 'UsvApiController@saveLocation');
    Route::post('/remove-location', 'UsvApiController@removeLocation');
    Route::post('/save-report-setting', 'UsvApiController@saveReportSetting');
    Route::post('/remove-report-setting', 'UsvApiController@removeReportSetting');
    Route::post('/search-report', 'UsvApiController@searchReport');
    Route::post('/remove-report', 'UsvApiController@removeReport');
    //Route::post('/upload-image', 'UsvApiController@uploadImage');
    Route::post('/update-report', 'ReportApiController@updateReport');
    Route::post('/remove-room', 'DeviceApiController@removeRoom');
    Route::post('/search-product', 'DeviceApiController@searchProduct');
    Route::post('/search-device', 'DeviceApiController@searchDevice');
    Route::post('/search-room', 'DeviceApiController@searchRoom');
    Route::post('/edit-setting', 'DeviceApiController@editSetting');
    Route::post('/device-verify', 'DeviceApiController@deviceVerify');
    Route::post('/upload-image', 'UploadApiController@uploadImage');
    Route::post('/test-verify', 'TestApiController@testVerify');
    Route::post('/check-email', 'UsvApiController@checkEmail');
    Route::post('/upload-course-image', 'UploadApiController@uploadCourseImage');
    Route::post('/upload-chapter-image', 'UploadApiController@uploadChapterImage');
    Route::post('/course-score', 'UploadApiController@courseScore');
    Route::post('/course-comment', 'UploadApiController@courseComment');
    Route::post('/course-comment-reply', 'UploadApiController@courseCommentReply');
    Route::post('/remove-all-comment', 'UploadApiController@removeAllComment');
    Route::post('/disable-comment', 'UploadApiController@disableComment');
});
