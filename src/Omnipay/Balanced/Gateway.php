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
            'marketPlace' => ''
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

    public function getMarketPlace()
    {
        return $this->getParameter('marketPlace');
    }

    public function setMarketPlace($value)
    {
        return $this->setParameter('marketPlace', $value);

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

//    public function updateCard(array $parameters = array())
//    {
//        return $this->createRequest('\Omnipay\Balanced\Message\UpdateCardRequest', $parameters);
//    }
//
//    public function deleteCard(array $parameters = array())
//    {
//        return $this->createRequest('\Omnipay\Balanced\Message\DeleteCardRequest', $parameters);
//    }
}
