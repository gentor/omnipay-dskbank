<?php

use Omnipay\DskBank\Gateway;
use Omnipay\Omnipay;

require '../vendor/autoload.php';
$config = include 'config.php';

echo '<pre>';

/** @var Gateway $gateway */
$gateway = Omnipay::create('DskBank');
$gateway->setUserName($config['userName'])
    ->setPassword($config['password'])
    ->setTestMode($config['testMode']);

$response = $gateway->status($_GET)->send();
//$response = $gateway->statusExtended($_GET)->send();

$orders = $gateway->getLastOrders([
    'size' => 5,
    'from' => '20200926000000',
    'to' => '20200928000000',
    'transactionStates' => 'CREATED,APPROVED,DEPOSITED,DECLINED,REVERSED,REFUNDED',
])->send();

dd(
    $response->getData(),
    $response->isSuccessful(),
    $response->getCode(),
    $response->getMessage(),
    $orders->getData()
);
