<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Create Customer Request
 */
class CreateCustomerRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();
        if ($this->getName()) {
            $data['name'] = $this->getName();
        }

        if ($this->getEmail()) {
            $data['email'] = $this->getEmail();
        }
        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/customers';
    }
}
