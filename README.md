# Laravel Paytm Wallet

[![Join the chat at https://gitter.im/laravel-paytm-wallet/Lobby](https://badges.gitter.im/laravel-paytm-wallet/Lobby.svg)](https://gitter.im/laravel-paytm-wallet/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

For Laravel 5.0 and above

## Introduction
Integrate paytm wallet in your laravel application easily with this package. This package uses official Paytm PHP SDK's.

## License
Laravel Paytm Wallet open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Getting Started
To get started add the following package to your `composer.json` file using this command.

    composer require anandsiddharth/laravel-paytm-wallet

## Configuring
When composer installs Laravel Paytm Wallet library successfully, register the `Anand\LaravelPaytmWallet\PaytmWalletServiceProvider` in your `config/app.php` configuration file.

```php
'providers' => [
    // Other service providers...
    Anand\LaravelPaytmWallet\PaytmWalletServiceProvider::class,
],
```
Also, add the `PaytmWallet` facade to the `aliases` array in your `app` configuration file:

```php
'aliases' => [
    // Other aliases
    'PaytmWallet' => Anand\LaravelPaytmWallet\Facades\PaytmWallet::class,
],
```
#### One more step to go....
On your `config/services.php` add the following configuration

```php
'paytm-wallet' => [
    'env' => 'production', // values : (local | production)
    'merchant_id' => 'YOUR_MERCHANT_ID',
    'merchant_key' => 'YOUR_MERCHANT_KEY',
    'merchant_website' => 'YOUR_WEBSITE',
    'channel' => 'YOUR_CHANNEL',
    'industry_type' => 'YOUR_INDUSTRY_TYPE',
],
```
Note : All the credentials mentioned are provided by Paytm after signing up as merchant.

## Usage

```php
<?php

namespace App\Http\Controllers;

use PaytmWallet;

class OrderController extends Controller
{
    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function order()
    {
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
          'order' => $order->id,
          'user' => $user->id,
          'mobile_number' => $user->phonenumber,
          'email' => $user->email,
          'amount' => $order->amount,
          'callback_url' => 'http://example.com/payment/status'
        ]);
        return $payment->receive();
    }

    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');
        
        $response = $transaction->response() // To get raw response as object
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        
        if($transaction->isSuccessful()){
          //Transaction Successful
        }else if($transaction->isFailed()){
          //Transaction Failed
        }else if($transaction->isOpen()){
          //Transaction Open/Processing
        }
        
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id
    }    
}
```

Make sure the `callback_url` you have mentioned while receiving payment is `post` on your `routes.php` file, Example see below:

```php
Route::post('/payment/status', 'OrderController@paymentCallback');
```
Important: The `callback_url` must not be csrf protected [Check out here to how to do that](https://laracasts.com/discuss/channels/general-discussion/l5-disable-csrf-middleware-on-certain-routes)
### Get transaction status/information using order id

```php
<?php

namespace App\Http\Controllers;

use PaytmWallet;

class OrderController extends Controller
{
    /**
    * Obtain the transaction status/information.
    *
    * @return Object
    */
    public function statusCheck(){
        $status = PaytmWallet::with('status');
        $status->prepare(['order' => $order->id]);
        $status->check();
        
        $response = $status->response() // To get raw response as object
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=txn-status-api-description
        
        if($status->isSuccessful()){
          //Transaction Successful
        }else if($status->isFailed()){
          //Transaction Failed
        }else if($status->isOpen()){
          //Transaction Open/Processing
        }
        
        //get important parameters via public methods
        $status->getOrderId(); // Get order id
        $status->getTransactionId(); // Get transaction id
    }
}
```

## Support on Beerpay

[![Beerpay](https://beerpay.io/anandsiddharth/laravel-paytm-wallet/badge.svg?style=beer-square)](https://beerpay.io/anandsiddharth/laravel-paytm-wallet)  [![Beerpay](https://beerpay.io/anandsiddharth/laravel-paytm-wallet/make-wish.svg?style=flat-square)](https://beerpay.io/anandsiddharth/laravel-paytm-wallet?focus=wish)
