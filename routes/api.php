<?php

# API Version 1
Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1'], function ($api) {

    # --- Auth
    Route::group(['prefix' => 'auth'], function ($api) {
        # Login
        $api->post('login', ['as' => 'api.v1.auth.login', 'uses' => 'AuthController@login']);
        # Logout
        $api->post('logout', ['as' => 'api.v1.auth.logout', 'uses' => 'AuthController@logout']);
        # Forgot / Reset Password
        $api->post('reset-password', ['as' => 'api.v1.auth.reset', 'uses' => 'AuthController@reset']);

    });
});

# -- API Version 3
Route::group(['prefix' => 'v3', 'namespace' => 'Api\V3'], function ($api) {

    # --- Auth
    Route::group(['prefix' => 'auth'], function ($api) {
        # Forgot / Reset Password
        $api->post('reset-initiate', ['as' => 'api.v3.auth.reset-initiate', 'uses' => 'AuthController@resetInitiate']);
        # Reset Change
        $api->post('reset-change', ['as' => 'api.v3.auth.reset-change', 'uses' => 'AuthController@resetChange']);
    });
});
