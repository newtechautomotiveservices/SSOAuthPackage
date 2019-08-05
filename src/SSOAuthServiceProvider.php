<?php

namespace Newtech\SSOAuth;

use Illuminate\Support\ServiceProvider;

class SSOAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // register our controller
        $this->app->make('Newtech\SSOAuth\SSOAuthController');
        $this->loadViewsFrom(__DIR__.'/views', 'ssoauth');
        $this->publishes([
            __DIR__ . '/config' => config_path('ssoauth')
        ], 'config');
        $this->publishes([
            __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');
        $this->app['router']->aliasMiddleware('ssoauth' , \Newtech\SSOAuth\Middleware\SSOAuthCheck::class);
        $this->app['router']->aliasMiddleware('ssoroutecheck' , \Newtech\SSOAuth\Middleware\SSORouteCheck::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }
}
