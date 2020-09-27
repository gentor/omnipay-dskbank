<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\RuntimeException;

class PurchaseRequest extends AbstractCurlRequest
{
    /**
     * Get order number
     *
     * @return string|null
     */
    public function getMdOrder(): ?string
    {
        return $this->getParameter('mdOrder');
    }

    /**
     * Set order number
     *
     * @param string $mdOrder
     * @return $this
     * @throws RuntimeException
     */
    public function setMdOrder(string $mdOrder): self
    {
        return $this->setParameter('mdOrder', $mdOrder);
    }

    /**
     * Get binding id
     *
     * @return string
     */
    public function getBindingId(): ?string
    {
        return $this->getParameter('bindingId');
    }

    /**
     * Set binding id
     *
     * @param string $bindingId
     * @return $this
     * @throws RuntimeException
     */
    public function setBindingId(string $bindingId): self
    {
        return $this->setParameter('bindingId', $bindingId);
    }

    /**
     * Get client's IP-address
     *
     * @return string
     */
    public function getIp(): ?string
    {
        return $this->getParameter('ip');
    }

    /**
     * Set client's IP-address
     *
     * @param string $ip
     * @return $this
     * @throws RuntimeException
     */
    public function setIp(string $ip): self
    {
        return $this->setParameter('ip', $ip);
    }

    /**
     * Get CVC code (if needed)
     *
     * @return int
     */
    public function getCvc(): ?int
    {
        return $this->getParameter('cvc');
    }

    /**
     * Set CVC code (if needed)
     *
     * @param int $cvc
     * @return $this
     * @throws RuntimeException
     */
    public function setCvc(int $cvc): self
    {
        return $this->setParameter('cvc', $cvc);
    }

    /**
     * Get user's email address
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->getParameter('email');
    }

    /**
     * Set user's email address
     *
     * @param string $email
     * @return $this
     * @throws RuntimeException
     */
    public function setEmail(string $email): self
    {
        return $this->setParameter('email', $email);
    }

    /**
     * Method name from bank API
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'rest/paymentOrderBinding.do';
    }

    /**
     * Response class name. Method will be ignored if class name passed to constructor third parameter
     *
     * @return string
     */
    public function getResponseClass(): string
    {
        return 'PurchaseResponse';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('mdOrder', 'bindingId', 'ip');

        $data = [
            'mdOrder' => $this->getMdOrder(),
            'bindingId' => $this->getBindingId(),
            'ip' => $this->getIp(),
        ];
        
        if ($language = $this->getLanguage()) {
            $data['language'] = $language;
        }
        
        if ($cvc = $this->getCvc()) {
            $data['cvc'] = $cvc;
        }
        
        if ($email = $this->getEmail()) {
            $data['email'] = $email;
        }
        
        return $data;
    }
}
