<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('cardReference', 'amount');

        $data = array();
        $data['amount'] = $this->getAmountInteger();
        $data['description'] = $this->getDescription();
        $data['appears_on_statement_as'] = $this->getStatementDescriptor();
        $data['source_uri'] = $this->getCardId();
        if ($this->getMerchantId()) {
            $data['on_behalf_of_uri'] = $this->getMerchantId();
        }

        return $data;
    }

    public function getEndpoint()
    {
        return $this->baseEndpoint . $this->getCustomerId() . '/debits';
    }

    public function setStatementDescriptor($value)
    {
        return $this->setParameter('statementDescriptor', $value);
    }

    public function getStatementDescriptor()
    {
        return $this->getParameter('statementDescriptor');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }
}
