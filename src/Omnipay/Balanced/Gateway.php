<?php

namespace Omnipay\Balanced;

use Omnipay\Common\AbstractGateway;
use Omnipay\Balanced\Message\PurchaseRequest;
use Omnipay\Balanced\Message\RefundRequest;

/**
 * Balanced Gateway
 *
 * @link https://balanced.com/docs/api
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Balanced';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey' => '',
            'marketplace' => ''
        );
    }

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getMarketplace()
    {
        return $this->getParameter('marketplace');
    }

    public function setMarketplace($value)
    {
        return $this->setParameter('marketplace', $value);

    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\PurchaseRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\RefundRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\FetchTransactionRequest', $parameters);
    }

    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\CreateCardRequest', $parameters);
    }

    public function fetchToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\FetchTokenRequest', $parameters);
    }
}
