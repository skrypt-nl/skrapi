<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function boot() {
        $api_versions = config('api.versions', ['v1']);
        $namespace_prefix = config('api.namespace_prefix', 'API');

        if ($namespace_prefix !== '') $namespace_prefix .= '/';

        foreach ($api_versions as $api_version) {
            Route::group([
                'middleware' => ['api', 'api_version:' . $api_version],
                'namespace'  => 'App\Http\Controllers\\' . str_replace('/', '\\', $namespace_prefix) . $api_version,
                'prefix'     => strtolower($namespace_prefix) . $api_version,
            ], function ($router) use ($api_version, $namespace_prefix) {
                require base_path("routes/{$namespace_prefix}{$api_version}.php");

                Route::any('/{any?}', function ($version) use ($api_version) {
                    return response(
                        json_encode(['error' => 'Invalid endpoint.', 'api_version' => $api_version]),
                        404
                    )->header(
                        'Content-Type',
                        'application/json'
                    );
                });
            });
        }

        Route::group([
            'middleware' => ['api'],
            'prefix'     => strtolower($namespace_prefix) . '{version}',
        ], function ($router) {
            Route::any('/{any?}', function ($version) {
                return response(
                    json_encode(['error' => 'API version does not exist.', 'api_version' => $version]),
                    404
                )->header(
                    'Content-Type',
                    'application/json'
                );
            });
        });
    }
}
