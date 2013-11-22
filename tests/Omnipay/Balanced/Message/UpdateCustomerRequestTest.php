<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Tests\TestCase;

class UpdateCustomerRequestTest extends TestCase
{
    public $request;

    public function setUp()
    {
        $this->request = new UpdateCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setMarketPlace('TEST-MP1TCNbswn3s3I2UxnZyM7Pq');
        $this->request->setApiKey('ak-test-2DBryLFR3BBam1CipbWEGSO6gqVOBKghP');
        $this->request->setCustomer(array(
            'uri' => '/v1/customers/CU2XyScd3YhAqUfXYbGXAtlS'
        ));
        $this->request->setToken('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC3JhGvAIeDRkwSGib4pkpIP');
    }

    public function testDataWithoutToken()
    {
        $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException');
        $this->request->setToken(null);
        $this->request->getData();
    }

    public function testDataWithoutCustomer()
    {
        $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException');
        $this->request->setCustomer(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('UpdateCustomerSuccess.txt');
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
        $this->setMockHttpResponse('UpdateCustomerFailure.txt');
        $this->request->setToken('invalid-card-uri');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('uri-not-understood', $response->getErrorCode());
    }
}
