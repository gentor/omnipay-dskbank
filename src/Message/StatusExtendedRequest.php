<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\RuntimeException;

class StatusExtendedRequest extends AbstractCurlRequest
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
        return 'rest/getOrderStatusExtended.do';
    }

    /**
     * Response class name. Method will be ignored if class name passed to constructor third parameter
     *
     * @return string
     */
    public function getResponseClass(): string
    {
        return 'StatusExtendedResponse';
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
        $data = [];
        
        if ($order = $this->getOrderId()) {
            $data['orderId'] = $order;
        } elseif ($order = $this->getOrderNumber()) {
            $data['orderNumber'] = $order;
        } else {
            throw new InvalidRequestException('No orderId or orderNumber provided');
        }

        if ($language = $this->getLanguage()) {
            $data['language'] = $language;
        }

        return $data;
    }
}
