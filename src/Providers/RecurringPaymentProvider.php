<?php

namespace Anand\LaravelPaytmWallet\Providers;
use Anand\LaravelPaytmWallet\Traits\HasTransactionStatus;

class RecurringPaymentProvider extends PaytmWalletProvider
{
    use HasTransactionStatus;

    private $parameters = null;
    private $view = 'paytmwallet::transact';

    public function prepare($params = array ()) {
        $defaults = ['order'           => null,
                     'user'            => null,
                     'amount'          => null,
                     'callback_url'    => null,
                     'email'           => null,
                     'mobile_number'   => null,
                     'subs_service_id' => null,
                     'amount_type'     => null,
                     'frequency'       => null,
                     'frequency_unit'  => null,];

        $_p = array_merge($defaults, $params);
        foreach ($_p as $key => $value) {

            if ($value == null) {

                throw new \Exception(' \'' . $key
                                     . '\' parameter not specified in array passed in prepare() method');

                return false;
            }
        }
        $this->parameters = $_p;

        return $this;
    }

    public function receive() {
        if ($this->parameters == null) {
            throw new \Exception("prepare() method not called");
        }

        return $this->beginTransaction();
    }

    private function beginTransaction() {
        $params = ['REQUEST_TYPE'     => 'SUBSCRIBE',
                   'MID'              => $this->merchant_id,
                   'ORDER_ID'         => $this->parameters['order'],
                   'CUST_ID'          => $this->parameters['user'],
                   'INDUSTRY_TYPE_ID' => $this->industry_type,
                   'CHANNEL_ID'       => $this->channel,
                   'TXN_AMOUNT'       => $this->parameters['amount'],
                   'WEBSITE'          => $this->merchant_website,
                   'CALLBACK_URL'     => $this->parameters['callback_url'],
                   'MOBILE_NO'        => $this->parameters['mobile_number'],
                   'EMAIL'            => $this->parameters['email'],

                   'SUBS_SERVICE_ID'     => $this->parameters['user'] . 'prime',
                   'SUBS_AMOUNT_TYPE'    => $this->parameters['amount_type'],
                   'SUBS_FREQUENCY'      => $this->parameters['frequency'],
                   'SUBS_FREQUENCY_UNIT' => $this->parameters['frequency_unit'],
                   'SUBS_ENABLE_RETRY'   => $this->parameters['enable_retry'],
                   'SUBS_EXPIRY_DATE'    => $this->parameters['expiry_date'],
                   'SUBS_START_DATE'     => $this->parameters['start_date'],
                   'SUBS_GRACE_DAYS'     => $this->parameters['grace_days'],
                   'SUBS_PPI_ONLY'       => $this->parameters['ppi_only'],

        ];

        return view('paytmwallet::form')
                ->with('view', $this->view)
                ->with('params', $params)
                ->with('txn_url', $this->paytm_txn_url)
                ->with('checkSum', getChecksumFromArray($params, $this->merchant_key));
    }

    public function view($view) {
        if ($view) {
            $this->view = $view;
        }

        return $this;
    }

    public function getOrderId() {
        return $this->response()['ORDERID'];
    }

    public function getSubscriptionId() {
        return $this->response()['SUBS_ID'];
    }

    public function getTransactionId() {
        return $this->response()['TXNID'];
    }

}
