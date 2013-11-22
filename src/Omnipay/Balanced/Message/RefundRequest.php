<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Refund Request
 */
class RefundRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $data = array();
        $data['amount'] = $this->getAmountInteger();

        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        $data['debit_uri'] = $this->getDebitId();
        return $data;
    }

    public function getEndpoint()
    {
        return $this->baseEndpoint . $this->getCustomerId() . '/refunds';
    }
}
