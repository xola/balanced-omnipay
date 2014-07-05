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

    public function getMarketplace()
    {
        return $this->getParameter('marketplace');

    }

    public function setMarketplace($value)
    {
        return $this->setParameter('marketplace', $value);
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

        return $this->response = new Response($this, $httpResponse->json());
    }

    protected function getCardData()
    {
        $data = array();
        $card = $this->getCard();

        $card->validate();

        // Required fields
        $data['card_number'] = $card->getNumber();
        $data['expiration_month'] = $card->getExpiryMonth();
        $data['expiration_year'] = $card->getExpiryYear();
        $data['security_code'] = $card->getCvv();

        // Optional fields. Balanced throws a validation error if empty fields are sent, so set them only if not empty.
        $this->setData($data, 'name', $card->getName());
        $this->setData($data, 'phone_number', $card->getPhone());
        $this->setData($data, 'street_address', trim($card->getAddress1() . ' ' . $card->getAddress2()));
        $this->setData($data, 'city', $card->getCity());
        $this->setData($data, 'postal_code', $card->getPostcode());

        return $data;
    }

    /**
     * A helper function that sets a field into a data array only if the value is not empty.
     *
     * @param array $data
     * @param string $key
     * @param mixed $value
     */
    private function setData(&$data, $key, $value)
    {
        if (!empty($value)) $data[$key] = $value;
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

    // Need the below setters and getters for accessing this data within createCardRequest.send

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
