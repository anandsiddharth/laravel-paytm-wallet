<?php

namespace Anand\LaravelPaytmWallet\Contracts;

interface Factory
{
    /**
     * Get Paytm Wallet Provider
     *
     * @param  string  $driver
     * @return \Anand\LaravelPaytmWallet\Contracts\Provider
     */
    
    public function driver($do = null);
}