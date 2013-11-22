<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Tests\TestCase;

class CreateCustomerRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CreateCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setMarketPlace('TEST-MP1TCNbswn3s3I2UxnZyM7Pq');
        $this->request->setApiKey('ak-test-2DBryLFR3BBam1CipbWEGSO6gqVOBKghP');
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.balancedpayments.com/v1/customers', $this->request->getEndpoint());
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertCount(0, $data);

        $this->request->setEmail('test@example.com');
        $data = $this->request->getData();
        $this->assertCount(1, $data);
        $this->assertEquals('test@example.com', $data['email']);

        $this->request->setName('Test');
        $data = $this->request->getData();
        $this->assertCount(2, $data);
        $this->assertEquals('Test', $data['name']);
    }


    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CreateCustomerSuccess.txt');
        /** @var Response $response */
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $data = $response->getData();
        $this->assertSame('/v1/customers/CU2XyScd3YhAqUfXYbGXAtlS', $data['uri']);
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->request->setApiKey('ak-test-wrong-api-key');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('authentication-required', $response->getErrorCode());
    }
}
