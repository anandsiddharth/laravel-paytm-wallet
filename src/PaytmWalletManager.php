<?php

namespace Anand\LaravelPaytmWallet;

use Illuminate\Support\Manager;
use Illuminate\Http\Request;
class PaytmWalletManager extends Manager implements Contracts\Factory{
	

	private $config;



	public function with($driver){
		return $this->driver($driver);
	}

	protected function createReceiveDriver(){
		$this->config = $this->app['config']['services.paytm-wallet'];

		return $this->buildProvider(
			'Anand\LaravelPaytmWallet\Providers\ReceivePaymentProvider',
			$this->config
			);
	}

	protected function createStatusDriver(){
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Anand\LaravelPaytmWallet\Providers\StatusCheckProvider',
			$this->config
			);
	}

	protected function createBalanceDriver(){
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Anand\LaravelPaytmWallet\Providers\BalanceCheckProvider',
			$this->config
			);
	}

	protected function createAppDriver(){
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Anand\LaravelPaytmWallet\Providers\PaytmAppProvider',
			$this->config
			);
	}
	

	public function getDefaultDriver(){
		throw new \Exception('No driver was specified. - Laravel Paytm Wallet');
	}

	public function buildProvider($provider, $config){
		return new $provider(
			$this->app['request'],
			$config
			);
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