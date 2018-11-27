<?php

namespace Anand\LaravelPaytmWallet\Contracts;

interface Provider
{
    /**
     * Return raw response.
     *
     * @return object
     */
    public function response();

}
