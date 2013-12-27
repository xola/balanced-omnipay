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
        $data['debit_uri'] = $this->getDebitId();
        $data['amount'] = $this->getAmountInteger();
        if ($this->getStatementDescriptor()) {
            $data['appears_on_statement_as'] = $this->getStatementDescriptor();
        }
        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        return $data;
    }

    public function getEndpoint()
    {
        return $this->baseEndpoint . $this->getCustomerId() . '/refunds';
    }

    public function setStatementDescriptor($value)
    {
        return $this->setParameter('statementDescriptor', $value);
    }

    public function getStatementDescriptor()
    {
        return $this->getParameter('statementDescriptor');
    }
}
