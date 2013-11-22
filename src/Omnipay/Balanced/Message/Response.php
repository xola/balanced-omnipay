<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Balanced Response
 */
class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return !isset($this->data['request_id']);
    }

    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            return $this->data['description'];
        }
    }

    public function getErrorCode()
    {
        if (!$this->isSuccessful()) {
            return $this->data['category_code'];
        }
    }

    public function getTransactionReference()
    {
        if (isset($this->data['_type']) && 'debit' === $this->data['_type']) {
            return json_encode(array(
                'debit' => $this->data['uri'],
                'customer' => $this->data['customer']['uri']
            ));
        }

        if (isset($this->data['_type']) && 'refund' === $this->data['_type']) {
            return json_encode(array(
                'refund' => $this->data['uri']
            ));
        }
    }

    public function getCardReference()
    {
        if (isset($this->data['_type']) && 'card' === $this->data['_type'] && $this->data['customer']) {
            return json_encode(array(
                'card' => $this->data['uri'],
                'customer' => $this->data['customer']['uri']
            ));
        }
    }
}
