<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $models = [
            'User',
        ];

        foreach ($models as $model)
        {
            $this->app->bind('App\Repositories\Contracts\\'.$model.'Repository', 'App\Repositories\Repository\\'.$model.'RepositoryEloquent');
        }

        // Auth
        $this->app->bind('App\\Repositories\\Auth\\Contract\\AuthRepository', 'App\\Repositories\\Auth\\Service\\AuthRepositorySentinel');

        //:end-bindings:
    }
}
