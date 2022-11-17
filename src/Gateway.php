<?php

namespace Omnipay\DskBank;

use Composer\CaBundle\CaBundle;
use Http\Discovery\HttpClientDiscovery;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Http\Client;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\DskBank\Message\AuthorizeRequest;
use Omnipay\DskBank\Message\CardBindRequest;
use Omnipay\DskBank\Message\CardUnbindRequest;
use Omnipay\DskBank\Message\DepositRequest;
use Omnipay\DskBank\Message\GetCardBindingsRequest;
use Omnipay\DskBank\Message\GetClientBindingsRequest;
use Omnipay\DskBank\Message\GetLastOrdersRequest;
use Omnipay\DskBank\Message\PurchaseRequest;
use Omnipay\DskBank\Message\RefundRequest;
use Omnipay\DskBank\Message\ReverseRequest;
use Omnipay\DskBank\Message\StatusExtendedRequest;
use Omnipay\DskBank\Message\StatusRequest;
use Omnipay\DskBank\Message\VerifyEnrollmentRequest;

/**
 * Class Gateway.
 *
 * Works with dskbank.bg gateway.
 * Supports test mode.
 * Implemented all methods from provided pdf instead of adding a card to SSL list
 *
 * @author Evgeni Nurkov
 * @company CloudCart
 * @package Omnipay\DskBank
 * @link https://uat.dskbank.bg/sandbox/integration/api/rest/rest.html
 * @link http://mcsign.dskbank.bg:7778/archive/ecom/EPG_Manual.zip
 *
 * @method NotificationInterface acceptNotification(array $options = array())
 * @method RequestInterface completeAuthorize(array $options = array())
 * @method RequestInterface capture(array $options = array())
 * @method RequestInterface completePurchase(array $options = array())
 * @method RequestInterface fetchTransaction(array $options = [])
 * @method RequestInterface void(array $options = array())
 * @method RequestInterface createCard(array $options = array())
 * @method RequestInterface updateCard(array $options = array())
 * @method RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    /**
     * Test gateway url
     *
     * @var string
     */
    public const TEST_URL = 'https://ecomtest.dskbank.bg/payment/';
    public const TEST_URL_2022 = 'https://uat.dskbank.bg/payment/';

    /**
     * Production gateway url
     *
     * @var string
     */
    public const PRODUCTION_URL = 'https://ecommerce.dskbank.bg/payment/';
    public const PRODUCTION_URL_2022 = 'https://epg.dskbank.bg/payment/';

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName(): string
    {
        return 'DskBank';
    }

    /**
     * Define gateway parameters, in the following format:
     *
     * [
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => ['billing', 'login'], // enum variable, first item is default
     * ];
     *
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'apiVersion' => 2022,
            'testMode' => true,
            'endpoint' => self::TEST_URL,
            'userName' => '',
            'password' => '',
            'orderNumber' => ''
        ];
    }

    /**
     * Get the global default HTTP client.
     *
     * @return ClientInterface
     */
    protected function getDefaultHttpClient()
    {
        $httpClient = HttpClientDiscovery::find();

        if ($httpClient instanceof \Http\Adapter\Guzzle6\Client) {
            file_put_contents(
                $tempCaBundleFile = tempnam(sys_get_temp_dir(), 'openssl-ca-bundle-'),
                file_get_contents(CaBundle::getBundledCaBundlePath()) .
                file_get_contents(__DIR__ . '/../resources/ca-bundle.pem')
            );

            $httpClient = \Http\Adapter\Guzzle6\Client::createWithConfig([
                'verify' => $tempCaBundleFile,
            ]);
        }

        return new Client($httpClient);
    }

    public function setApiVersion($value): self
    {
        if ($value == 2022) {
            $this->setEndpoint($this->getTestMode() ? self::TEST_URL_2022 : self::PRODUCTION_URL_2022);
        } else {
            $this->setEndpoint($this->getTestMode() ? self::TEST_URL : self::PRODUCTION_URL);
        }

        return $this->setParameter('apiVersion', $value);
    }

    /**
     * Set gateway test mode. Also changes URL
     *
     * @param bool $value
     * @return $this
     */
    public function setTestMode($testMode): self
    {
        if ($this->getParameter('apiVersion') == 2022) {
            $this->setEndpoint($testMode ? self::TEST_URL_2022 : self::PRODUCTION_URL_2022);
        } else {
            $this->setEndpoint($testMode ? self::TEST_URL : self::PRODUCTION_URL);
        }

        return $this->setParameter('testMode', $testMode);
    }

    /**
     * Get endpoint URL
     *
     * @return string
     */
    public function getEndpoint(): ?string
    {
        return $this->getParameter('endpoint');
    }

    /**
     * Set endpoint URL
     *
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint(string $endpoint): self
    {
        return $this->setParameter('endpoint', $endpoint);
    }

    /**
     * Get gateway user name
     *
     * @return string
     */
    public function getUserName(): ?string
    {
        return $this->getParameter('userName');
    }

    /**
     * Set gateway user name
     *
     * @param string $userName
     * @return $this
     */
    public function setUserName(string $userName): self
    {
        return $this->setParameter('userName', $userName);
    }

    /**
     * Get gateway password
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->getParameter('password');
    }

    /**
     * Set gateway password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        return $this->setParameter('password', $password);
    }

    /**
     * Get order number
     *
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->getParameter('orderNumber');
    }

    /**
     * Set order number
     *
     * @param int|string $orderNumber
     * @return $this
     */
    public function setOrderNumber($orderNumber): self
    {
        return $this->setParameter('orderNumber', $orderNumber);
    }

    /**
     * Get language (ISO 639-1)
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->getParameter('language');
    }

    /**
     * Set language (ISO 639-1)
     *
     * @param string $language
     * @return AbstractRequest|$this
     */
    public function setLanguage(string $language): self
    {
        return $this->setParameter('language', $language);
    }

    /**
     * Does gateway supports deposit?
     *
     * @return bool
     */
    public function supportsDeposit(): bool
    {
        return method_exists($this, 'deposit');
    }

    /**
     * Does gateway supports status?
     *
     * @return bool
     */
    public function supportsStatus(): bool
    {
        return method_exists($this, 'status');
    }

    /**
     * Does gateway supports status extended?
     *
     * @return bool
     */
    public function supportsStatusExtended(): bool
    {
        return method_exists($this, 'statusExtended');
    }

    /**
     * Does gateway supports reverse?
     *
     * @return bool
     */
    public function supportsReverse(): bool
    {
        return method_exists($this, 'reverse');
    }

    /**
     * Does gateway supports card 3ds enrollment verifying?
     *
     * @return bool
     */
    public function supportsVerifyEnrollment(): bool
    {
        return method_exists($this, 'verifyEnrollment');
    }

    /**
     * Does gateway supports order list?
     *
     * @return bool
     */
    public function supportsGetLastOrders(): bool
    {
        return method_exists($this, 'getLastOrders');
    }

    /**
     * Does gateway supports card binding?
     *
     * @return bool
     */
    public function supportsCardBind(): bool
    {
        return method_exists($this, 'cardBind');
    }

    /**
     * Does gateway supports card unbinding?
     *
     * @return bool
     */
    public function supportsCardUnbind(): bool
    {
        return method_exists($this, 'cardUnbind');
    }

    /**
     * Does gateway supports client's card bindings list?
     *
     * @return bool
     */
    public function supportsGetClientBindings(): bool
    {
        return method_exists($this, 'getClientBindings');
    }

    /**
     * Does gateway supports cards bindings list?
     *
     * @return bool
     */
    public function supportsGetCardBindings(): bool
    {
        return method_exists($this, 'getCardBindings');
    }

    /**
     * Authorize request
     *
     * @param array $options
     * @return AbstractRequest|AuthorizeRequest
     */
    public function authorize(array $options = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * Deposit request
     *
     * @param array $options
     * @return AbstractRequest|DepositRequest
     */
    public function deposit(array $options = [])
    {
        return $this->createRequest(DepositRequest::class, $options);
    }

    /**
     * Order status request
     *
     * @param array $options
     * @return AbstractRequest|StatusRequest
     */
    public function status(array $options = [])
    {
        return $this->createRequest(StatusRequest::class, $options);
    }

    /**
     * Order status extended request
     *
     * @param array $options
     * @return AbstractRequest|StatusExtendedRequest
     */
    public function statusExtended(array $options = [])
    {
        return $this->createRequest(StatusExtendedRequest::class, $options);
    }

    /**
     * Reverse order
     *
     * @param array $options
     * @return AbstractRequest|ReverseRequest
     */
    public function reverse(array $options = [])
    {
        return $this->createRequest(ReverseRequest::class, $options);
    }

    /**
     * Refund sum from order
     *
     * @param array $options
     * @return AbstractRequest|RefundRequest
     */
    public function refund(array $options = [])
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * Verify card 3DS enrollment
     *
     * @param array $options
     * @return AbstractRequest|VerifyEnrollmentRequest
     */
    public function verifyEnrollment(array $options = [])
    {
        return $this->createRequest(VerifyEnrollmentRequest::class, $options);
    }

    /**
     * Get last orders list
     *
     * @param array $options
     * @return AbstractRequest|GetLastOrdersRequest
     */
    public function getLastOrders(array $options = [])
    {
        return $this->createRequest(GetLastOrdersRequest::class, $options);
    }

    /**
     * Purchase request
     *
     * @param array $options
     * @return AbstractRequest|PurchaseRequest
     */
    public function purchase(array $options = [])
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Bind card
     *
     * @param array $options
     * @return AbstractRequest|CardBindRequest
     */
    public function cardBind(array $options = [])
    {
        return $this->createRequest(CardBindRequest::class, $options);
    }

    /**
     * Unbind card
     *
     * @param array $options
     * @return AbstractRequest|CardUnbindRequest
     */
    public function cardUnbind(array $options = [])
    {
        return $this->createRequest(CardUnbindRequest::class, $options);
    }

    /**
     * Get client's card bindings
     *
     * @param array $options
     * @return AbstractRequest|GetClientBindingsRequest
     */
    public function getClientBindings(array $options = [])
    {
        return $this->createRequest(GetClientBindingsRequest::class, $options);
    }

    /**
     * Get card's bindings
     *
     * @param array $options
     * @return AbstractRequest|GetCardBindingsRequest
     */
    public function getCardBindings(array $options = [])
    {
        return $this->createRequest(GetCardBindingsRequest::class, $options);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}
