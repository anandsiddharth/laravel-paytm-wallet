<?php

namespace Lakshmaji\LaravelPaytmWallet\Contracts;

interface Factory
{
    /**
     * Get Paytm Wallet Provider
     *
     * @param  string  $driver
     * @return \Lakshmaji\LaravelPaytmWallet\Contracts\Provider
     */

    public function driver($do = null);
}
