<?php

namespace Lakshmaji\LaravelPaytmWallet;


use Illuminate\Support\ServiceProvider;


class PaytmWalletServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Lakshmaji\LaravelPaytmWallet\Contracts\Factory', function ($app) {
            // $this->app->singleton('PaytmWallet', function ($app) {
            return new PaytmWalletManager($app);
        });
    }


    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'paytmwallet');
    }



    public function provides()
    {
        return ['Lakshmaji\LaravelPaytmWallet\Contracts\Factory'];
    }
}
