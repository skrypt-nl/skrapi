<?php

use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\v1\UsersController;

Route::get('/', function() {
    return response()->json([
        'message' => 'Successfully connected!',
        'api_version' => config('api.version'),
    ]);
});

Route::post('/login', 'AuthController@login');

Route::post('/logout', 'AuthController@logout')->middleware('auth:api');
Route::post('/register', 'AuthController@register');

Orion::resource('users', UsersController::class)->withSoftDeletes();
