<?php

namespace Anand\LaravelPaytmWallet\Providers;

use Illuminate\Http\Request;


class PaytmWalletProvider{

	protected $request;
	protected $response;
	protected $paytm_txn_url;
	protected $paytm_txn_status_url;
	protected $paytm_balance_check_url;

	protected $merchant_key;
	protected $merchant_id;
	protected $merchant_website;
	protected $industry_type;
	protected $channel;



	public function __construct(Request $request, $config){
		$this->request = $request;
		
		if ($config['env'] == 'production') {
			$domain = 'secure.paytm.in';
		}else{
			$domain = 'pguat.paytm.com';
		}
		$this->paytm_txn_url = 'https://'.$domain.'/oltp-web/processTransaction';
		$this->paytm_txn_status_url = 'https://'.$domain.'/oltp/HANDLER_INTERNAL/TXNSTATUS';
		$this->paytm_refund_url = 'https://'.$domain.'/oltp/HANDLER_INTERNAL/REFUND';
		$this->paytm_balance_check = 'https://'.$domain.'/oltp/HANDLER_INTERNAL/checkBalance';

		$this->merchant_key = $config['merchant_key'];
		$this->merchant_id = $config['merchant_id'];
		$this->merchant_website  = $config['merchant_website'];
		$this->industry_type = $config['industry_type'];
		$this->channel = $config['channel'];
	}

	public function getResponse(){
		return $this->response;
	}


	public function api_call($url, $params){
		$jsonResponse = "";
		$responseParamList = array();
		$JsonData =json_encode($params);
		$postData = 'JsonData='.urlencode($JsonData);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                         
			'Content-Type: application/json', 
			'Content-Length: ' . strlen($postData))                                                                       
		);  
		$jsonResponse = curl_exec($ch);   
		return $responseParamList = json_decode($jsonResponse,true);
	}


}