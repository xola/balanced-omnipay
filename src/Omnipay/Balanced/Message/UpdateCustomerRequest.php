<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Update Customer Request
 */
class UpdateCustomerRequest extends CreateCustomerRequest
{
    public function getData()
    {
        $this->validate('customer', 'token');
        $data = parent::getData();
        if ($this->getToken()) {
            // Token exists. Add card to the customer
            $data['card_uri'] = $this->getToken();
        }
        return $data;
    }

    public function getEndpoint()
    {
        $customer = $this->getCustomer();
        return $this->baseEndpoint . $customer['uri'];
    }

    public function getHttpMethod()
    {
        return 'PUT';
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }
}
