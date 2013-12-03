<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $endpoint = 'https://api.balancedpayments.com/v1';
    protected $baseEndpoint = 'https://api.balancedpayments.com';

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

    abstract public function getEndpoint();

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            null,
            $data
        );

        $httpResponse = $httpRequest
            ->setHeader('Authorization', 'Basic ' . base64_encode($this->getApiKey() . ':'))
            ->send();

        // Log http requests for debugging purpose
        // todo: remove before final push
        // $logger = new \Monolog\Logger('my_logger');
        // $logger->pushHandler(new \Monolog\Handler\StreamHandler('/var/log/php/debug.log', \Monolog\Logger::DEBUG));
        // $logger->pushHandler(new \Monolog\Handler\FirePHPHandler());
        // $logger->addInfo($httpRequest . "\n\n" . $httpResponse);

        return $this->response = new Response($this, $httpResponse->json());
    }

    protected function getCardData()
    {
        $this->getCard()->validate();

        $data = array();
        $data['card_number'] = $this->getCard()->getNumber();
        $data['expiration_month'] = $this->getCard()->getExpiryMonth();
        $data['expiration_year'] = $this->getCard()->getExpiryYear();
        $data['security_code'] = $this->getCard()->getCvv();
        $data['name'] = $this->getCard()->getName();
        $data['phone_number'] = $this->getCard()->getPhone();
        $data['street_address'] = $this->getCard()->getAddress1() . ' ' . $this->getCard()->getAddress2();
        $data['city'] = $this->getCard()->getCity();
        $data['postal_code'] = $this->getCard()->getPostcode();
        //        $data['country_code'] = $this->getCard()->getParameter('country_code');
        //        $data['meta'] = $this->getCard()->getParameter('meta');
        //        $data['verify'] = $this->getCard()->getParameter('verify');

        return $data;
    }


    /**
     * Helper getter to get card, customer and debit ids from the cardReference , transactionReference
     */
    protected function getCardId()
    {
        $cardRef = $this->getCardReference();
        if ($cardRef) {
            $cardRefObj = json_decode($cardRef);
            return $cardRefObj->card;
        }
    }

    protected function getCustomerId()
    {
        $cardRef = $this->getCardReference();
        if ($cardRef) {
            $cardRefObj = json_decode($cardRef);
            return $cardRefObj->customer;
        }

        $transactionRef = $this->getTransactionReference();
        if ($transactionRef) {
            $transactionRefObj = json_decode($transactionRef);
            return $transactionRefObj->customer;
        }
    }

    protected function getDebitId()
    {
        $transactionRef = $this->getTransactionReference();
        if ($transactionRef) {
            $transactionRefObj = json_decode($transactionRef);
            return $transactionRefObj->debit;
        }
    }

    protected function getTransactionUri()
    {
        $transactionRef = $this->getTransactionReference();
        if ($transactionRef) {
            $transactionRefObj = json_decode($transactionRef);

            if (!empty($transactionRefObj->refund)) {
                return $transactionRefObj->refund;
            }

            if (!empty($transactionRefObj->debit)) {
                return $transactionRefObj->debit;
            }
        }
    }

    /**
     * Need these below setters and getter for accessing this data withing createCardRequest.send
     **/
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getName()
    {
        return $this->getParameter('name');
    }
}
