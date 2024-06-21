<?php

namespace Webkul\AuthorizeNet\Providers;

use Illuminate\Support\ServiceProvider;

class AuthorizeNetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../Http/routes.php';

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'authorizenet');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'authorizenet');

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Merge the stripe connect's configuration with the admin panel
     */
    public function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/paymentmethods.php', 'payment_methods'
        );
    }
}
