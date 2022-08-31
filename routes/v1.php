<?php

Route::get('/', function() {
    return response()->json([
        'message' => 'Successfully connected!',
        'api_version' => config('api.version'),
    ]);
});
