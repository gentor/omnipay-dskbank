<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\RuntimeException;

class RefundRequest extends AbstractCurlRequest
{
    /**
     * Get order id
     *
     * @return string
     */
    public function getOrderId(): ?string
    {
        return $this->getParameter('orderId');
    }

    /**
     * Set order id
     *
     * @param string $orderId
     * @return $this
     * @throws RuntimeException
     */
    public function setOrderId(string $orderId): self
    {
        return $this->setParameter('orderId', $orderId);
    }

    /**
     * Method name from bank API
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'rest/refund.do';
    }

    /**
     * Response class name. Method will be ignored if class name passed to constructor third parameter
     *
     * @return string
     */
    public function getResponseClass(): string
    {
        return 'RefundResponse';
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
        $this->validate('orderId', 'amount');

        $data = [
            'orderId' => $this->getOrderId(),
            'amount' => $this->getAmount(),
        ];

        if ($currency = $this->getCurrency()) {
            $data['currency'] = $currency;
        }

        return $data;
    }
}
