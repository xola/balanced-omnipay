<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Fetch Transaction Request
 */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');
    }

    public function getEndpoint()
    {
        return $this->baseEndpoint . $this->getTransactionUri();
    }

    public function getHttpMethod()
    {
        return 'GET';
    }
}
