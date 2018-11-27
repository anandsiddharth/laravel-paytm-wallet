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
    const STATUS_SUCCESSFUL = 'TXN_SUCCESS';
    const STATUS_FAILURE = 'TXN_FAILURE';
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';

    const RESPONSE_SUCCESSFUL="01";
    const RESPONSE_CANCELLED = "141";
    const RESPONSE_FAILED = "227";
    const RESPONSE_PAGE_CLOSED = "810";
    const REPSONSE_REFUND_ALREADY_RAISED = "617";
    const RESPONSE_CANCELLED_CUSTOMER = "8102";
    const RESPONSE_CANCELLED_CUSTOMER_INSUFFICIENT_BALANCE = "8103";

    protected static function getFacadeAccessor()
    {
        return 'Anand\LaravelPaytmWallet\Contracts\Factory';
    }
}