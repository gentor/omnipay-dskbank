<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\AbstractRequest;

class AuthorizeRequest extends AbstractCurlRequest
{
    /**
     * Get page view
     *
     * @return string|null
     */
    public function getPageView(): ?string
    {
        return $this->getParameter('pageView');
    }

    /**
     * Defaults are DESKTOP or MOBILE if you implemented it in your payment page template.
     *
     * @param string $pageView
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setPageView(string $pageView): self
    {
        return $this->setParameter('pageView', $pageView);
    }

    /**
     * Get session timeout in seconds
     *
     * @return int|null
     */
    public function getSessionTimeoutSecs(): ?int
    {
        return $this->getParameter('sessionTimeoutSecs');
    }

    /**
     * Set session timeout in seconds
     *
     * @param int $sessionTimeoutSecs
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setSessionTimeoutSecs(int $sessionTimeoutSecs): self
    {
        return $this->setParameter('sessionTimeoutSecs', $sessionTimeoutSecs);
    }

    /**
     * Get id of previously created binding. Use only if you work with bindings.
     *
     * @return string|null
     */
    public function getBindingId(): ?string
    {
        return $this->getParameter('bindingId');
    }

    /**
     * Set id of previously created binding. Use only if you work with bindings.
     *
     * @param string $bindingId
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setBindingId(string $bindingId): self
    {
        return $this->setParameter('bindingId', $bindingId);
    }

    /**
     * Get features (AUTO_PAYMENT or VERIFY)
     *
     * @return string|null
     */
    public function getFeatures(): ?string
    {
        return $this->getParameter('features');
    }

    /**
     * Set features (AUTO_PAYMENT or VERIFY)
     *
     * @param string $features
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setFeatures(string $features): self
    {
        return $this->setParameter('features', $features);
    }

    /**
     * Get order expiration date in yyyy-MM-ddTHH:mm:ss
     *
     * @return string|null
     */
    public function getExpirationDate(): ?string
    {
        return $this->getParameter('expirationDate');
    }

    /**
     * Set order expiration date in yyyy-MM-ddTHH:mm:ss
     *
     * @param string $expirationDate
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setExpirationDate(string $expirationDate): self
    {
        return $this->setParameter('expirationDate', $expirationDate);
    }

    /**
     * Get fail payment url
     *
     * @return string|null
     */
    public function getFailUrl(): ?string
    {
        return $this->getParameter('failUrl');
    }

    /**
     * Set fail payment url
     *
     * @param string $failUrl
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setFailUrl(string $failUrl): self
    {
        return $this->setParameter('failUrl', $failUrl);
    }

    /**
     * Get client id for bindings
     *
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->getParameter('clientId');
    }

    /**
     * Set client id for bindings
     *
     * @param string $clientId
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setClientId(string $clientId): self
    {
        return $this->setParameter('clientId', $clientId);
    }

    /**
     * Additional merchant login if you using one.
     *
     * @return string|null
     */
    public function getMerchantLogin(): ?string
    {
        return $this->getParameter('merchantLogin');
    }

    /**
     * Additional merchant login if you using one.
     *
     * @param string $merchantLogin
     * @return AbstractRequest|$this
     * @throws RuntimeException
     */
    public function setMerchantLogin(string $merchantLogin): self
    {
        return $this->setParameter('merchantLogin', $merchantLogin);
    }

    /**
     * Is order two stepped?
     *
     * @return bool|null
     */
    public function getTwoStep(): ?bool
    {
        return $this->getParameter('two_stage');
    }

    /**
     * Set two step order authentication
     *
     * @param bool $twoStage
     * @return $this
     * @throws RuntimeException
     */
    public function setTwoStep(bool $twoStage): self
    {
        return $this->setParameter('two_stage', $twoStage);
    }

    /**
     * Method name from bank API
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return $this->getTwoStep() ? 'rest/registerPreAuth.do' : 'rest/register.do';
    }

    /**
     * Response class name. Method will be ignored if class name passed to constructor third parameter
     *
     * @return string
     */
    public function getResponseClass(): string
    {
        return 'AuthorizeResponse';
    }

    public function getJsonParams()
    {
        return $this->getParameter('jsonParams');
    }

    public function setJsonParams(string $value): self
    {
        return $this->setParameter('jsonParams', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('orderNumber', 'amount', 'returnUrl');

        $data = [
            'orderNumber' => $this->getOrderNumber(),
            'amount' => $this->getAmount(),
            'returnUrl' => $this->getReturnUrl(),
        ];

        $extraParameters = [
            'currency', 'description', 'language', 'pageView', 'sessionTimeoutSecs', 'features',
            'bindingId', 'expirationDate', 'failUrl', 'clientId', 'merchantLogin', 'taxSystem',
            'jsonParams',
        ];

        foreach ($extraParameters as $parameter) {
            $getter = 'get' . ucfirst($parameter);

            if (method_exists($this, $getter)) {
                $value = $this->{$getter}();

                if ($value !== null) {
                    $data[$parameter] = $value;
                }
            }
        }

        return $data;
    }
}
