<?php

return [
    'namespaces' => [
        'models' => 'App\\Models\\',
        'controllers' => 'App\\Http\\Controllers\\v1\\',
    ],
    'auth' => [
        'guard' => 'api',
    ],
    'specs' => [
        'info' => [
            'title' => env('APP_NAME'),
            'description' => 'Laravel API',
            'terms_of_service' => 'https://www.skrypt.nl/algemene-voorwaarden',
            'contact' => [
                'name' => 'Skrypt',
                'url' => 'https://www.skrypt.nl',
                'email' => 'info@skrypt.nl',
            ],
            'license' => [
                'name' => 'Affero General Public License 3 (AGPL-3.0)',
                'url' => "http://opensource.org/licenses/AGPL-3.0",
            ],
            'version' => '1.0.0',
        ],
        'servers' => [
            ['url' => env('APP_URL').'/v1', 'description' => 'Default Environment'],
        ],
        'tags' => [],
    ],
    'transactions' => [
        'enabled' => false,
    ],
    'search' => [
        'case_sensitive' => true,
        /*
         |--------------------------------------------------------------------------
         | Max Nested Depth
         |--------------------------------------------------------------------------
         |
         | This value is the maximum depth of nested filters.
         | You will most likely need this to be maximum at 1, but
         | you can increase this number, if necessary. Please
         | be aware that the depth generate dynamic rules and can slow
         | your application if someone sends a request with thousands of nested
         | filters.
         |
         */
        'max_nested_depth' => 1,
    ],

    'use_validated' => false,
];
