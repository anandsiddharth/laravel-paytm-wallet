<?php

namespace Anand\LaravelPaytmWallet\Providers;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use Anand\LaravelPaytmWallet\Traits\HasTransactionStatus;
use Illuminate\Http\Request;


class RefundPaymentProvider extends PaytmWalletProvider{
    use HasTransactionStatus;
    
	private $parameters = null;
    protected $response;

    public function prepare($params = array()){
		$defaults = [
            'order' => NULL,
            'reference' => NULL,
            'amount' => NULL,
            'transaction' => NULL
		];

		$_p = array_merge($defaults, $params);
		foreach ($_p as $key => $value) {

			if ($value == NULL) {
				
				throw new \Exception(' \''.$key.'\' parameter not specified in array passed in prepare() method');
				
				return false;
			}
		}
		$this->parameters = $_p;
		return $this;
    }
    
    private function beginTransaction(){
        $params = array();
        $params["MID"] = $this->merchant_id;
        $params["ORDERID"] = $this->parameters['order']; 
        $params["REFID"] = $this->parameters['reference'];
        $params["TXNTYPE"] = 'REFUND';
        $params["REFUNDAMOUNT"] = $this->parameters['amount'];
        $params["TXNID"] = $this->parameters['transaction'];
		$chk = getChecksumFromArray($params, $this->merchant_key);
        $params['CHECKSUM'] = $chk;
		$this->response = $this->api_call_new($this->paytm_refund_url, $params);
		return $this;
	}

    public function initiate(){
		if ($this->parameters == null) {
			throw new \Exception("prepare() method not called");
		}
        $this->beginTransaction();
        return $this;
    }
    
    public function response(){
		return $this->response;
    }

    public function isRefundAlreadyRaised() {
        if ($this->isFailed() && $this->response()['RESPCODE'] == PaytmWallet::REPSONSE_REFUND_ALREADY_RAISED) {
            return true;
        }
        return false;
    }
	
}
