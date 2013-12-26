<?php

namespace Omnipay\Balanced;

use Omnipay\Balanced\Message\CreateCardRequest;
use Omnipay\Balanced\Message\FetchTokenRequest;
use Omnipay\Balanced\Message\FetchTransactionRequest;
use Omnipay\Balanced\Message\PurchaseRequest;
use Omnipay\Balanced\Message\RefundRequest;
use Omnipay\Common\AbstractGateway;

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

    /**
     * @param array $parameters
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\RefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\CreateCardRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return FetchTokenRequest
     */
    public function fetchToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Balanced\Message\FetchTokenRequest', $parameters);
    }
}
