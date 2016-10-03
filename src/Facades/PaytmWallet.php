<?php

namespace Anand\LaravelPaytmWallet\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravel\Socialite\SocialiteManager
 */
class PaytmWallet extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    protected static function getFacadeAccessor()
    {
        return 'Anand\LaravelPaytmWallet\Contracts\Factory';
    }
}