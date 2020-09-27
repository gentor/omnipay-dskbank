# Omnipay: DskBank

**[DSK Bank](https://dskbank.bg) gateway for Omnipay payment processing library**

Inspired from [omnipay-paymentgateru](https://github.com/pinguinjkeke/omnipay-paymentgateru)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements DSK Bank support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `gentor/omnipay-dskbank` with Composer:

```
composer require league/omnipay gentor/omnipay-dskbank
```

## Basic Usage

### Purchase
```php
$gateway = Omnipay::create('DskBank');
$gateway->setUserName($config['userName'])
    ->setPassword($config['password'])
    ->setTestMode($config['testMode']);

$response = $gateway->authorize([
    'orderNumber' => time(),
    'amount' => 5 * 100,
    'description' => 'Dsk Bank Test Purchase',
    'returnUrl' => 'http://dskbank.test/return.php',
    'failUrl' => 'http://dskbank.test/return.php',
])->send();

$bankReference = $response->getTransactionReference();

if ($response->isRedirect()) {
    // Redirect to offsite payment gateway
    $response->redirect();
} else {
    // Payment failed
    echo $response->getMessage();
}
```

### Complete Purchase
```php
$status = $gateway->status($_GET)->send();
$statusExtended = $gateway->statusExtended($_GET)->send();

$orders = $gateway->getLastOrders([
    'size' => 5,
    'from' => '20200926000000',
    'to' => '20200928000000',
    'transactionStates' => 'APPROVED,REFUNDED',
])->send();

$refund = $gateway->refund([
   'orderId' => $bankReference,
   'amount' => $price * 100
])->send();

$success = $refund->isSuccessful();
```