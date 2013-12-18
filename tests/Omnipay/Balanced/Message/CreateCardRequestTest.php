<?php

namespace Omnipay\Balanced\Message;

use Omnipay\Tests\TestCase;

class CreateCardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setCard($this->getValidCard());
        $this->request->setMarketplace('TEST-MP1TCNbswn3s3I2UxnZyM7Pq');
        $this->request->setApiKey('ak-test-2DBryLFR3BBam1CipbWEGSO6gqVOBKghP');
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.balancedpayments.com/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards', $this->request->getEndpoint());
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertNotNull($data);
    }

    public function testSendSuccessToken()
    {
        $this->request->setCard(null);
        $this->request->setToken('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0');

        $this->setMockHttpResponse(array('FetchCardSuccess.txt', 'CreateCustomerSuccess.txt', 'UpdateCustomerSuccess.txt'));
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNotNull($response->getCardReference());
        $cardRefArray = json_decode($response->getCardReference(), true);
        $this->assertCount(2, $cardRefArray);
        $this->assertEquals('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0', $cardRefArray['card']);
        $this->assertEquals('/v1/customers/CU2XyScd3YhAqUfXYbGXAtlS', $cardRefArray['customer']);

    }

    public function testSendSuccessAssociatedToken()
    {
        $this->request->setCard(null);
        $this->request->setToken('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0');

        $this->setMockHttpResponse(array('FetchCardByReferenceSuccess.txt'));
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNotNull($response->getCardReference());
        $cardRefArray = json_decode($response->getCardReference(), true);
        $this->assertCount(2, $cardRefArray);
        $this->assertEquals('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/accounts/CU5PIWAaIM2UBvRBfVpdMjzq/cards/CC5ZboW6WNIAtYj3RH42Oo0', $cardRefArray['card']);
        $this->assertEquals('/v1/customers/CU5PIWAaIM2UBvRBfVpdMjzq', $cardRefArray['customer']);

    }

    public function testSendSuccessCard()
    {
        $this->setMockHttpResponse(array('CreateCardSuccess.txt', 'CreateCustomerSuccess.txt', 'UpdateCustomerSuccess.txt'));
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNotNull($response->getCardReference());
        $cardRefArray = json_decode($response->getCardReference(), true);
        $this->assertCount(2, $cardRefArray);
        $this->assertEquals('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC3JhGvAIeDRkwSGib4pkpIP', $cardRefArray['card']);
        $this->assertEquals('/v1/customers/CU2XyScd3YhAqUfXYbGXAtlS', $cardRefArray['customer']);
    }

    public function testSendFailureFetchCard()
    {
        $this->request->setCard(null);
        $this->request->setToken('/v1/marketplaces/TEST-MP1TCNbswn3s3I2UxnZyM7Pq/cards/CC5ZboW6WNIAtYj3RH42Oo0');
        $this->setMockHttpResponse(array('FetchCardFailure.txt'));
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNotNull($response->getErrorCode());
        $this->assertNull($response->getCardReference());
    }

    public function testSendFailureCreateCustomer()
    {
        $this->setMockHttpResponse(array('CreateCardSuccess.txt', 'CreateCustomerFailure.txt'));
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNotNull($response->getErrorCode());
        $this->assertNull($response->getCardReference());
    }

    public function testSendFailureUpdateCustomer()
    {
        $this->setMockHttpResponse(array('CreateCardSuccess.txt', 'CreateCustomerSuccess.txt', 'UpdateCustomerFailure.txt'));
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNotNull($response->getErrorCode());
        $this->assertNull($response->getCardReference());
    }
}
