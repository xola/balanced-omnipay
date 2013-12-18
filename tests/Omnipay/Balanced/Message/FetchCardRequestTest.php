<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class FetchCardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new FetchCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setToken('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0');
        $this->request->setMarketplace('TEST-MP1TCNbswn3s3I2UxnZyM7Pq');
        $this->request->setApiKey('ak-test-2DBryLFR3BBam1CipbWEGSO6gqVOBKghP');
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.balancedpayments.com/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0', $this->request->getEndpoint());
    }

    public function testDataWithoutAnyParams()
    {
        $data = $this->request->getData();
        $this->assertNull($data);

        $this->request->setToken(null);
        try {
            $this->request->getData();
        } catch (InvalidRequestException $e) {
            return;
        }
        $this->fail('Exception expected');
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('FetchCardSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->request->setToken('/invalid-token');

        $this->setMockHttpResponse('FetchCardFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('not-found', $response->getErrorCode());
    }
}
