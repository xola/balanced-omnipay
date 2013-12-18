<?php

namespace Omnipay\Balanced;

use Omnipay\Tests\GatewayTestCase;
use Guzzle\Http\Message\RequestInterface;


class GatewayTest extends GatewayTestCase
{

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
            'amount' => '10.23',
            'statementDescriptor' => 'Merchant Name',
            'description' => 'Debit 10.23 dollars on customers card for merchant'
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


//    public function testRefund()
//    {
//        $request = $this->gateway->refund(array('amount' => '10.00'));
//
//        $this->assertInstanceOf('Omnipay\Balanced\Message\RefundRequest', $request);
//        $this->assertSame('10.00', $request->getAmount());
//    }
//
//    public function testFetchTransaction()
//    {
//        $request = $this->gateway->fetchTransaction(array());
//
//        $this->assertInstanceOf('Omnipay\Balanced\Message\FetchTransactionRequest', $request);
//    }

    /**
     * @integrationTest
     */
    public function testFullFlow()
    {
        // Create card
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Test Customer',
            'email' => 'testcustomer@test.com',
        );
        $request = $this->gateway->createCard($params);

        $this->assertInstanceOf('Omnipay\Balanced\Message\CreateCardRequest', $request);
        $response = $request->send();
        $data = $response->getData();

        // Purchase using the card reference
        $request = $this->gateway->purchase(array(
            'cardReference' => $response->getCardReference(),
            'amount' => 10.23,
            'statementDescriptor' => 'Merchant Name',
            'description' => 'Debit 10.23 dollars on customers card for merchant'
        ));
        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $data = $response->getData();
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Merchant Name', $data['appears_on_statement_as']);
        $this->assertEquals('Debit 10.23 dollars on customers card for merchant', $data['description']);

        // Fetch details of debit transaction
        $debitRef = $response->getTransactionReference();
        $request = $this->gateway->fetchTransaction(array(
            'transactionReference' => $debitRef
        ));
        $response = $request->send();
        $data = $response->getData();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1023, $data['amount']);
        $this->assertEquals('Merchant Name', $data['appears_on_statement_as']);
        $this->assertEquals('Debit 10.23 dollars on customers card for merchant', $data['description']);

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
        $this->assertEquals('Merchant Name', $data['appears_on_statement_as']);
        $this->assertEquals('Debit 10.23 dollars on customers card for merchant', $data['description']);

    }

//
//    public function testUpdateCard()
//    {
//        $request = $this->gateway->updateCard(array('cardReference' => 'cus_1MZSEtqSghKx99'));
//
//        $this->assertInstanceOf('Omnipay\Balanced\Message\UpdateCardRequest', $request);
//        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCardReference());
//    }
//
//    public function testDeleteCard()
//    {
//        $request = $this->gateway->deleteCard(array('cardReference' => 'cus_1MZSEtqSghKx99'));
//
//        $this->assertInstanceOf('Omnipay\Balanced\Message\DeleteCardRequest', $request);
//        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCardReference());
//    }
}
