<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Exception\RuntimeException;

class VerifyEnrollmentRequest extends AbstractCurlRequest
{
    /**
     * Get pan (credit card number)
     *
     * @return string
     */
    public function getPan(): ?string
    {
        return $this->getParameter('pan');
    }

    /**
     * Set pan (credit card number)
     *
     * @param string $pan
     * @return $this
     * @throws RuntimeException
     */
    public function setPan(string $pan): self
    {
        return $this->setParameter('pan', $pan);
    }

    /**
     * Method name from bank API
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'rest/verifyEnrollment.do';
    }

    /**
     * Response class name. Method will be ignored if class name passed to constructor third parameter
     *
     * @return string
     */
    public function getResponseClass(): string
    {
        return 'VerifyEnrollmentResponse';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            'pan' => $this->getPan(),
        ];
    }
}
