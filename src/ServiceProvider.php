<?php

namespace Attla\Cookier;

use Attla\Cookier\Manager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cookier', function ($app) {
            return (new Manager($app->make('request')))->setPrefix(env('APP_PREFIX', ''));
        });
    }
}
