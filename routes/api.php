<?php
URL::forceSchema('https');
Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {
    Route::post('tutors/search', 'TutorsController@search');
    Route::post('yachts/search', 'YachtsController@search');
    Route::resource('requests', 'RequestsController', ['only' => 'store']);
    Route::get('reviews/block', 'ReviewsController@block');
    Route::resource('reviews', 'ReviewsController');
    Route::resource('stats', 'StatsController', ['only' => 'index']);
    // Route::post('cv/uploadPhoto', 'CvController@uploadPhoto');
    Route::resource('cv', 'CvController', ['only' => 'store']);
    Route::resource('stream', 'StreamController', ['only' => 'store']);
    // Route::resource('sms', 'SmsController');
});
