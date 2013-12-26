<?php

namespace Omnipay\Balanced;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(array('apiKey' => 'ak-test-2DBryLFR3BBam1CipbWEGSO6gqVOBKghP', 'marketplace' => 'TEST-MP1TCNbswn3s3I2UxnZyM7Pq'));
    }

    public function testPurchase()
    {
        $params = array(
            'cardReference' => '{"card":"\/v1\/marketplaces\/TEST-MP1TCNbswn3s3I2UxnZyM7Pq\/cards\/CC3D9myl48gjwocNVssBf7ES","customer":"\/v1\/customers\/CU3DJN2ZXR4592yuUzf9mPeU"}',
            'amount' => 10.23,
            'statementDescriptor' => 'Space Parts Inc.',
            'description' => '1 radion-accelerator core'
        );
        $request = $this->gateway->purchase($params);

        $this->assertInstanceOf('Omnipay\Balanced\Message\PurchaseRequest', $request);
        $this->assertSame('10.23', $request->getAmount());

        $this->setMockHttpResponse(array('PurchaseSuccess.txt'));

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $data = $response->getData();
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Merchant Name', $data['appears_on_statement_as']);
        $this->assertEquals('Debit 10.23 dollars on customers card for merchant', $data['description']);
    }

    /**
     * @group integration
     */
    public function testFullFlow()
    {
        // Create card
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => 'kaylee@serenity.com',
        );
        $request = $this->gateway->createCard($params);

        $this->assertInstanceOf('Omnipay\Balanced\Message\CreateCardRequest', $request);
        $response = $request->send();

        // Purchase using the card reference
        $request = $this->gateway->purchase(array(
            'cardReference' => $response->getCardReference(),
            'amount' => 10.23,
            'statementDescriptor' => 'Space Parts Inc.',
            'description' => '1 radion-accelerator core'
        ));
        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $data = $response->getData();
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Space Parts Inc.', $data['appears_on_statement_as']);
        $this->assertEquals('1 radion-accelerator core', $data['description']);

        // Fetch details of debit transaction
        $debitRef = $response->getTransactionReference();
        $request = $this->gateway->fetchTransaction(array(
            'transactionReference' => $debitRef
        ));
        $response = $request->send();
        $data = $response->getData();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Space Parts Inc.', $data['appears_on_statement_as']);
        $this->assertEquals('1 radion-accelerator core', $data['description']);

        // Refund the transaction using the transaction reference
        $request = $this->gateway->refund(array(
            'transactionReference' => $response->getTransactionReference(),
            'amount' => 5.93,
            'description' => 'Refunding 5.93 to customer'
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $data = $response->getData();
        $this->assertEquals(593, $data['amount']);
        $this->assertEquals('Refunding 5.93 to customer', $data['description']);

        // Fetch details of refund transaction
        $request = $this->gateway->fetchTransaction(array(
            'transactionReference' => $response->getTransactionReference(),
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(593, $data['amount']);
        $this->assertEquals('Refunding 5.93 to customer', $data['description']);

        // Fetch details of original debit transaction again
        $request = $this->gateway->fetchTransaction(array(
            'transactionReference' => $debitRef
        ));
        $response = $request->send();
        $data = $response->getData();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Space Parts Inc.', $data['appears_on_statement_as']);
        $this->assertEquals('1 radion-accelerator core', $data['description']);
    }
}
