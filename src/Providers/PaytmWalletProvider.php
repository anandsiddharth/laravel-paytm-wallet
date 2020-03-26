<?php

namespace Anand\LaravelPaytmWallet\Providers;
use Anand\LaravelPaytmWallet\Contracts\Provider as ProviderContract;
use Illuminate\Http\Request;
require __DIR__.'/../../lib/encdec_paytm.php';

class PaytmWalletProvider implements ProviderContract {

	protected $request;
	protected $response;
	protected $paytm_txn_url;
	protected $paytm_txn_status_url;
	protected $paytm_refund_url;
	protected $paytm_refund_status_url;
	protected $paytm_balance_check_url;

	protected $merchant_key;
	protected $merchant_id;
	protected $merchant_website;
	protected $industry_type;
	protected $channel;


	public function __construct(Request $request, $config){
		$this->request = $request;
		
		if ($config['env'] == 'production') {
			$domain = 'securegw.paytm.in';
		}else{
			$domain = 'securegw-stage.paytm.in';
		}
		$this->paytm_txn_url = 'https://'.$domain.'/theia/processTransaction';
		$this->paytm_txn_status_url = 'https://'.$domain.'/merchant-status/getTxnStatus';
		$this->paytm_refund_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/REFUND';
		$this->paytm_refund_status_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/getRefundStatus';
		$this->paytm_balance_check_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/getRefundStatus';

		$this->merchant_key = $config['merchant_key'];
		$this->merchant_id = $config['merchant_id'];
		$this->merchant_website  = $config['merchant_website'];
		$this->industry_type = $config['industry_type'];
		$this->channel = $config['channel'];
	}

	public function response(){
		$checksum = $this->request->get('CHECKSUMHASH');
		if(verifychecksum_e($this->request->post(), $this->merchant_key, $checksum) == "TRUE"){
		    return $this->response = $this->request->post();
		}
        	throw new \Exception('Invalid checksum');
	}

	public function getResponseMessage() {
    		return $this->response()['RESPMSG'];
   	}

	public function api_call($url, $params){
		return callAPI($url, $params);
	}

	public function api_call_new($url, $params){
		return callAPI($url, $params);
	}
}
