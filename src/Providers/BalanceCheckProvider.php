<?php

namespace Anand\LaravelPaytmWallet\Providers;
use Illuminate\Http\Request;
// require __DIR__.'/../../lib/encdec_paytm.php';

class BalanceCheckProvider extends PaytmWalletProvider{


	private $parameters = null;



	public function prepare($params = array()){
		$defaults = [
			'token' => NULL,
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

	public function check(){
		if ($this->parameters == null) {
			throw new \Exception("prepare() method not called");
		}
		return $this->beginTransaction();
	}

	private function beginTransaction(){

		$params = [
			'MID' => $this->merchant_id,
			'TOKEN' => $this->parameters['token']
		];
		return $this->api_call($this->paytm_balance_check_url, $params);
	}

}