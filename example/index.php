<?php
require '../vendor/autoload.php';
$config = include 'config.php';

use Omnipay\Omnipay;
use Omnipay\DskBank\Gateway;

echo '<pre>';

/** @var Gateway $gateway */
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
//    'two_stage' => true,
])->send();

// Process response
if ($response->isRedirect()) {
    // Redirect to offsite payment gateway
    $response->redirect();
} else {
    // Payment failed
    echo $response->getMessage();
}
