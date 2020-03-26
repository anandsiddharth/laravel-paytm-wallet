# Laravel Paytm Wallet

[![Latest Stable Version](https://poser.pugx.org/anandsiddharth/laravel-paytm-wallet/v/stable)](https://packagist.org/packages/anandsiddharth/laravel-paytm-wallet)
[![Total Downloads](https://poser.pugx.org/anandsiddharth/laravel-paytm-wallet/downloads)](https://packagist.org/packages/anandsiddharth/laravel-paytm-wallet)
[![License](https://poser.pugx.org/anandsiddharth/laravel-paytm-wallet/license)](https://packagist.org/packages/anandsiddharth/laravel-paytm-wallet)
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
**Note: For Laravel 5.5 and above auto-discovery takes care of below configuration.**

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
#### Add the paytm credentials to the `.env` file
```bash
PAYTM_ENVIRONMENT=local
PAYTM_MERCHANT_ID=YOUR_MERCHANT_ID_HERE
PAYTM_MERCHANT_KEY=YOUR_SECRET_KEY_HERE
PAYTM_MERCHANT_WEBSITE=YOUR_MERCHANT_WEBSITE
PAYTM_CHANNEL=YOUR_CHANNEL_HERE
PAYTM_INDUSTRY_TYPE=YOUR_INDUSTRY_TYPE_HERE
```


#### One more step to go....
On your `config/services.php` add the following configuration

```php
'paytm-wallet' => [
        'env' => env('PAYTM_ENVIRONMENT'), // values : (local | production)
        'merchant_id' => env('PAYTM_MERCHANT_ID'),
        'merchant_key' => env('PAYTM_MERCHANT_KEY'),
        'merchant_website' => env('PAYTM_MERCHANT_WEBSITE'),
        'channel' => env('PAYTM_CHANNEL'),
        'industry_type' => env('PAYTM_INDUSTRY_TYPE'),
],
```
Note : All the credentials mentioned are provided by Paytm after signing up as merchant.

#### Laravel 7 Changes
Our package is comptible with Laravel 7 but same_site setting is changed in default Laravel installation, make sure you change `same_site` to `null` in `config/session.php` or callback won't include cookies and you will be logged out when a payment is completed

```php
<?php

use Illuminate\Support\Str;

return [
  /...
  'same_site' => null,
];
```

## Usage


### Making a transaction
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
        
        $response = $transaction->response() // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        
        if($transaction->isSuccessful()){
          //Transaction Successful
        }else if($transaction->isFailed()){
          //Transaction Failed
        }else if($transaction->isOpen()){
          //Transaction Open/Processing
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
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
        
        $response = $status->response() // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=txn-status-api-description
        
        if($status->isSuccessful()){
          //Transaction Successful
        }else if($status->isFailed()){
          //Transaction Failed
        }else if($status->isOpen()){
          //Transaction Open/Processing
        }
        $status->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $status->getOrderId(); // Get order id
        $status->getTransactionId(); // Get transaction id
    }
}
```

### Initiating Refunds

```php
<?php

namespace App\Http\Controllers;

use PaytmWallet;

class OrderController extends Controller
{
    /**
    * Initiate refund.
    *
    * @return Object
    */
    public function refund(){
        $refund = PaytmWallet::with('refund');
        $refund->prepare([
            'order' => $order->id,
            'reference' => "refund-order-4", // provide refund reference for your future reference (should be unique for each order)
            'amount' => 300, // refund amount 
            'transaction' => $order->transaction_id // provide paytm transaction id referring to this order 
        ]);
        $refund->initiate();
        $response = $refund->response() // To get raw response as array
        
        if($refund->isSuccessful()){
          //Refund Successful
        }else if($refund->isFailed()){
          //Refund Failed
        }else if($refund->isOpen()){
          //Refund Open/Processing
        }else if($refund->isPending()){
          //Refund Pending
        }
    }
}
```

### Check Refund Status

```php
<?php

namespace App\Http\Controllers;

use PaytmWallet;

class OrderController extends Controller
{
    /**
    * Initiate refund.
    *
    * @return Object
    */
    public function refund(){
        $refundStatus = PaytmWallet::with('refund_status');
        $refundStatus->prepare([
            'order' => $order->id,
            'reference' => "refund-order-4", // provide reference number (the same which you have entered for initiating refund)
        ]);
        $refundStatus->check();
        
        $response = $refundStatus->response() // To get raw response as array
        
        if($refundStatus->isSuccessful()){
          //Refund Successful
        }else if($refundStatus->isFailed()){
          //Refund Failed
        }else if($refundStatus->isOpen()){
          //Refund Open/Processing
        }else if($refundStatus->isPending()){
          //Refund Pending
        }
    }
}
```

### Customizing transaction being processed page
Considering the modern app user interfaces, default "transaction being processed page" is too dull which comes with this package. If you would like to modify this, you have the option to do so. Here's how:
You just need to change 1 line in you `OrderController`'s code.

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
        return $payment->view('your_custom_view')->receive();
    }
```
Here `$payment->receive()` is replaced with `$payment->view('your_custom_view')->receive()`. Replace `your_custom_view` with your view name which resides in your `resources/views/your_custom_view.blade.php`.

And in your view file make sure you have added this line of code before `</body>` (i.e. before closing body tag), which redirects to payment gateway.

`@yield('payment_redirect')`

Here's a sample custom view: 

```html
<html>
<head>
</head>
<body>
    <h1>Custom payment message</h1>
    @yield('payment_redirect')
</body>
</html>
```

That's all folks!

## Support on Beerpay

[![Beerpay](https://beerpay.io/anandsiddharth/laravel-paytm-wallet/badge.svg?style=beer-square)](https://beerpay.io/anandsiddharth/laravel-paytm-wallet)  [![Beerpay](https://beerpay.io/anandsiddharth/laravel-paytm-wallet/make-wish.svg?style=flat-square)](https://beerpay.io/anandsiddharth/laravel-paytm-wallet?focus=wish)
