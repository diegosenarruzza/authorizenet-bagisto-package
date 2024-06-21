<?php

namespace Webkul\AuthorizeNet\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.layout.body.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('authorizenet::checkout.onepage.authorizenet-smart-button');
        });
    }
}
