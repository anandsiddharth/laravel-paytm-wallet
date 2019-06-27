<?php

namespace Lakshmaji\LaravelPaytmWallet;

use Illuminate\Support\Manager;
use Illuminate\Http\Request;

class PaytmWalletManager extends Manager implements Contracts\Factory
{


	private $config;



	public function with($driver)
	{
		return $this->driver($driver);
	}

	protected function createReceiveDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];

		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\ReceivePaymentProvider',
			$this->config
		);
	}

	protected function createStatusDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\StatusCheckProvider',
			$this->config
		);
	}

	protected function createBalanceDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\BalanceCheckProvider',
			$this->config
		);
	}

	protected function createAppDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\PaytmAppProvider',
			$this->config
		);
	}

	protected function createRefundDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\RefundPaymentProvider',
			$this->config
		);
	}

	protected function createRefundStatusDriver()
	{
		$this->config = $this->app['config']['services.paytm-wallet'];
		return $this->buildProvider(
			'Lakshmaji\LaravelPaytmWallet\Providers\RefundStatusCheckProvider',
			$this->config
		);
	}

	public function getDefaultDriver()
	{
		throw new \Exception('No driver was specified. - Laravel Paytm Wallet');
	}

	public function buildProvider($provider, $config)
	{
		return new $provider(
			$this->app['request'],
			$config
		);
	}
}
