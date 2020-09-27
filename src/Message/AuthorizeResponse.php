<?php

namespace Omnipay\DskBank\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class AuthorizeResponse extends AbstractCurlResponse implements RedirectResponseInterface
{
    /**
     * Does the response require a redirect?
     * Success response is a redirect
     *
     * @return bool
     */
    public function isRedirect(): ?bool
    {
        return $this->isSuccessful();
    }

    /**
     * Gets the redirect target url.
     *
     * @return string
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['formUrl'];
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->isSuccessful() ? $this->data['orderId'] : null;
    }
}
