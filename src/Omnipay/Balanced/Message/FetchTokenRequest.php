<?php

namespace Omnipay\Balanced\Message;

/**
 * Balanced Fetch Credit Card Request.
 */
class FetchTokenRequest extends AbstractRequest
{
    public function getData()
    {
        // Usually a cardReference is needed to fetch card details.
        // However for balanced gateway, we need to fetch the card based on the token generated. Hence we allow it too.
        if (!$this->getToken()) {
            // Requires card reference or token
            $this->validate('cardReference');
        }

    }

    public function getEndpoint()
    {
        if ($this->getToken()) {
            // Token is present. Construct end point based on card token.
            return $this->baseEndpoint . $this->getToken();
        } else {
            // Card reference is present. Construct end point based on card id
            return $this->endpoint . $this->getCardId();
        }
    }

    public function getHttpMethod()
    {
        return 'GET';
    }
}
