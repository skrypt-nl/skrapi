<?php

Route::get('/', function() {
    return response()->json([
        'message' => 'Successfully connected!',
        'api_version' => config('api.version'),
    ]);
});

Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');
Route::post('/register', 'AuthController@register');

