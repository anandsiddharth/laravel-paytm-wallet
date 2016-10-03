<?php

namespace Anand\LaravelPaytmWallet\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $driver
     * @return \Laravel\Socialite\Contracts\Provider
     */
    
    public function driver($do = null);
}