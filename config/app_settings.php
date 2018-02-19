<?php

return [
    'application' => [
        'api' => [
            'version'  => env('APP_API_VER', '2017-07-16 10:44:21'),
            'abs_path' => base_path(),
        ],
        'web' => [
            'version'  => env('APP_WEB_VER', '2017-07-16 10:44:21'),
            'abs_path' => base_path('../myre-web/'),
        ],
    ],

    // choose:
    // - git
    // - .env
    'versioning' => 'git',

    // Middleware applificatino
    'middleware' => [
        'user-type'             => env('MYRE_MIDDLEWARE_USER_TYPE', true),
        'user-customer-type'    => env('MYRE_MIDDLEWARE_USER_CUSTOMER_TYPE', true),
        'user-access'           => env('MYRE_MIDDLEWARE_USER_ACCESS', true),
    ],
    'demo' => [
        'portfolio_id'           => env('DEMO_PORTFOLIO_ID', 126),
        'customer_id'           => env('DEMO_CUSTOMER_ID', 36),
    ],
    'user-access-dataroom'      => env('MYRE_USER_ACCESS_DATAROOM', true),
    'user-access-property'      => env('MYRE_USER_ACCESS_PROPERTY', true),
    'bail-version'              => env('MYRE_BAIL_VERSION', 1),
    'web-base-url'              => env('APP_WEB_URL', 'http://myre.stagingapps.net/'),

];