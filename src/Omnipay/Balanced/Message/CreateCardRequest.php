<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Balanced\Message\FetchCardRequest;
use Omnipay\Balanced\Message\CreateCustomerRequest;
use Omnipay\Balanced\Message\UpdateCustomerRequest;

/**
 * Balanced Create Credit Card Request.
 */
class CreateCardRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('card');

        return $this->getCardData();
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/marketplaces/' . $this->getParameter('marketplace') . '/cards';
    }

    /**
     * This makes multiple api calls to get a card that can be charged
     * - Create a card if card data is sent OR Fetch the card if token is sent
     * - Create a customer if card is not associated with any customer
     * - Add the card to the created customer.
     */

    public function send()
    {
        if ($this->getCard()) {
            // New Card is being created from card data
            $fetchCardResponse = parent::send();
        } else {
            // Card is being created from token
            $request = new FetchCardRequest($this->httpClient, $this->httpRequest);
            $request->initialize(array(
                'token' => $this->getParameter('token'),
                'apiKey' => $this->getApiKey(),
                'marketplace' => $this->getMarketplace()
            ));
            $fetchCardResponse = $request->send();
        }

        if (!$fetchCardResponse->isSuccessful()) {
            // Fetch card response is not successful
            return $fetchCardResponse;
        }

        $cardData = $fetchCardResponse->getData();

        if ($cardData['customer']) {
            // Card is already associated with a customer. Nothing more to do.
            return $fetchCardResponse;
        }

        // Create new customer.
        $request = new CreateCustomerRequest($this->httpClient, $this->httpRequest);
        $request->initialize(array(
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'apiKey' => $this->getApiKey(),
            'marketplace' => $this->getMarketplace()
        ));

        $createCusResponse = $request->send();
        if (!$createCusResponse->isSuccessful()) {
            // Could not create customer. Return
            return $createCusResponse;
        }

        $cusData = $createCusResponse->getData();

        // Add card to the customer
        $request = new UpdateCustomerRequest($this->httpClient, $this->httpRequest);
        $request->initialize(array(
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'customer' => $cusData,
            'token' => $cardData['uri'],
            'apiKey' => $this->getApiKey(),
            'marketplace' => $this->getMarketplace()
        ));

        $updateCusResponse = $request->send();
        if (!$updateCusResponse->isSuccessful()) {
            // Could not add card to the customer. Return
            return $updateCusResponse;
        }

        $cusData = $updateCusResponse->getData();

        // Add customer data to the card manually and return to avoid making an additional api call to get updated Card
        $cardData['customer'] = $cusData;

        return $this->response = new Response($this, $cardData);
    }
}
