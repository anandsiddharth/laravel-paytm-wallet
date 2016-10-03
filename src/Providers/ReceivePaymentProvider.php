<?php

namespace Anand\LaravelPaytmWallet\Providers;
use Illuminate\Http\Request;
require __DIR__.'/../../lib/encdec_paytm.php';

class ReceivePaymentProvider extends PaytmWalletProvider{


	private $parameters = null;



	public function prepare($params = array()){
		$defaults = [
			'order' => NULL,
			'user' => NULL,
			'amount' => NULL
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

	public function receive(){
		if ($this->parameters == null) {
			throw new \Exception("prepare() method not called");
		}
		return $this->beginTransaction();
	}

	private function beginTransaction(){

		$params = [
			'MID' => $this->merchant_id,
			'ORDER_ID' => $this->parameters['order'],
			'CUST_ID' => $this->parameters['user'],
			'INDUSTRY_TYPE_ID' => $this->industry_type,
			'CHANNEL_ID' => $this->channel,
			'TXN_AMOUNT' => $this->parameters['amount'],
			'WEBSITE' => $this->merchant_website
		];
		return view('paytmwallet::transact')->with('params', $params)->with('txn_url', $this->paytm_txn_url)->with('checkSum', getChecksumFromArray($params, $this->merchant_key));
	}

}