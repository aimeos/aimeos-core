<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class PayPalExpressTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $serviceItem;
	private $order;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'paypalexpress' ) );

		$serviceItems = $serviceManager->searchItems( $search );

		if( ( $this->serviceItem = reset( $serviceItems ) ) === false ) {
			throw new \RuntimeException( 'No paypalexpress service item available' );
		}

		$this->object = new \Aimeos\MShop\Service\Provider\Payment\PayPalExpress( $this->context, $this->serviceItem );


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );

		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		if( ( $this->order = reset( $orderItems ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No Order found with statuspayment "%1$s" and type "%2$s"', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::TYPE_WEB ) );
		}


		$orderMock = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$this->context->getConfig()->set( 'mshop/order/manager/name', 'MockPayPal' );
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\MockPayPal', $orderMock );
	}


	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->serviceItem );
		unset( $this->order );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 16, count( $result ) );

		foreach( $result as $key => $item ) {
			$this->assertInstanceOf( 'Aimeos\MW\Criteria\Attribute\Iface', $item );
		}
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'paypalexpress.ApiUsername' => 'user',
			'paypalexpress.AccountEmail' => 'user@test.de',
			'paypalexpress.ApiPassword' => 'pw',
			'paypalexpress.ApiSignature' => '1df23eh67',
			'payment.url-cancel' => 'http://cancelUrl',
			'payment.url-success' => 'http://returnUrl'
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 16, count( $result ) );
		$this->assertEquals( null, $result['paypalexpress.ApiUsername'] );
		$this->assertEquals( null, $result['paypalexpress.AccountEmail'] );
		$this->assertEquals( null, $result['paypalexpress.ApiPassword'] );
		$this->assertEquals( null, $result['paypalexpress.ApiSignature'] );
		$this->assertEquals( null, $result['payment.url-cancel'] );
		$this->assertEquals( null, $result['payment.url-success'] );
	}

	public function testIsImplemented()
	{
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_REFUND ) );

		$this->assertFalse( $this->object->isImplemented( -1 ) );
	}


	public function testProcess()
	{
		$what = array( 'PAYMENTREQUEST_0_AMT' => 18.50 );
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=process method error';
		$success = '&ACK=Success&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&TOKEN=UT-99999999';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$helperForm = $this->object->process( $this->order );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId() );

		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();

		$attributeList = [];
		foreach( $attributes as $attribute ) {
			$attributeList[$attribute->getCode()] = $attribute;
		}

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Helper\\Form\\Iface', $helperForm );
		$this->assertEquals( 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=UT-99999999', $helperForm->getUrl() );
		$this->assertEquals( 'POST', $helperForm->getMethod() );
		$this->assertEquals( [], $helperForm->getValues() );

		$testData = array(
			'TOKEN' => 'UT-99999999'
		);

		foreach( $testData as $key => $value ) {
			$this->assertEquals( $attributeList[$key]->getValue(), $testData[$key] );
		}
	}


	public function testUpdateSync()
	{
		//DoExpressCheckout

		$what = array( 'TOKEN' => 'UT-99999999' );

		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=updatesync method error';
		$success = '&TOKEN=UT-99999999&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725&PAYERID=PaypalUnitTestBuyer&TRANSACTIONID=111111110&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=' . $this->order->getId();

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$response = array(
			'token' => 'UT-99999999',
			'PayerID' => 'PaypalUnitTestBuyer',
			'orderid' => $this->order->getId()
		);

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Iface', $this->object->updateSync( $response ) );

		//IPN Call
		$price = $orderBaseManager->getItem( $this->order->getBaseId() )->getPrice();
		$amount = $price->getValue() + $price->getCosts();
		$what = array(
			'residence_country' => 'US',
			'address_city' => 'San+Jose',
			'first_name' => 'John',
			'payment_status' => 'Completed',
			'invoice' => $this->order->getId(),
			'txn_id' => '111111111',
			'payment_amount' => $amount,
			'receiver_email' => 'selling2@metaways.de',
		);
		$error = 'INVALID';
		$success = 'VERIFIED';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );


		$response = array(
			'residence_country' => 'US',
			'receiver_email' => 'selling2@metaways.de',
			'address_city' => 'San+Jose',
			'first_name' => 'John',
			'payment_status' => 'Completed',
			'invoice' => $this->order->getId(),
			'txn_id' => '111111111',
			'payment_amount' => $amount
		);
		$testData = array(
			'TRANSACTIONID' => '111111111',
			'PAYERID' => 'PaypalUnitTestBuyer',
			'111111110' => 'Pending',
			'111111111' => 'Completed'
		);

		$orderItem = $this->object->updateSync( $response );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Iface', $orderItem );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE );
		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();
		$attrManager = $orderBaseManager->getSubManager( 'service' )->getSubManager( 'attribute' );

		$attributeList = [];
		foreach( $attributes as $attribute ) {
			//remove attr where txn ids as keys, because next test with same txn id would fail
			if( $attribute->getCode() === '111111110' || $attribute->getCode() === '111111111' ) {
				$attrManager->deleteItem( $attribute->getId() );
			}

			$attributeList[$attribute->getCode()] = $attribute;
		}

		foreach( $testData as $key => $value ) {
			$this->assertEquals( $attributeList[$key]->getValue(), $testData[$key] );
		}

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $orderItem->getPaymentStatus() );
	}


	public function testRefund()
	{
		$what = array(
			'METHOD' => 'RefundTransaction',
			'REFUNDSOURCE' => 'instant',
			'REFUNDTYPE' => 'Full',
			'TRANSACTIONID' => '111111111',
			'INVOICEID' => $this->order->getId()
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=refund method error';
		$success = 'REFUNDTRANSACTIONID=88888888&FEEREFUNDAMT=2.00&TOTALREFUNDAMT=24.00&CURRENCYCODE=EUR&REFUNDSTATUS=delayed&PENDINGREASON=echeck&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->refund( $this->order );

		$testData = array(
			'TOKEN' => 'UT-99999999',
			'TRANSACTIONID' => '111111111',
			'REFUNDTRANSACTIONID' => '88888888'
		);

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId() );
		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();

		$attributeList = [];
		foreach( $attributes as $attribute ) {
			$attributeList[$attribute->getCode()] = $attribute;
		}

		foreach( $testData as $key => $value ) {
			$this->assertEquals( $attributeList[$key]->getValue(), $testData[$key] );
		}

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUND, $this->order->getPaymentStatus() );
	}


	public function testCapture()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$baseItem = $orderBaseManager->getItem( $this->order->getBaseId() );

		$what = array(
			'METHOD' => 'DoCapture',
			'COMPLETETYPE' => 'Complete',
			'AUTHORIZATIONID' => '111111111',
			'INVNUM' => $this->order->getId(),
			'CURRENCYCODE' => $baseItem->getPrice()->getCurrencyId(),
			'AMT' => ( $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getCosts() )
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=capture method error';
		$success = 'AUTHORIZATIONID=112233&TRANSACTIONID=111111111&PARENTTRANSACTIONID=12212AD&TRANSACTIONTYPE=express-checkout&AMT=22.30&FEEAMT=3.33&PAYMENTSTATUS=Completed&PENDINGREASON=None&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->capture( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getPaymentStatus() );
	}

	public function testQueryPaymentReceived()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111'
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment received test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Completed&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getPaymentStatus() );
	}


	public function testQueryPaymentRefused()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment refused test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Expired&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUSED, $this->order->getPaymentStatus() );
	}


	public function testCancel()
	{
		$what = array(
			'METHOD' => 'DoVoid',
			'AUTHORIZATIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=cancel test method error';
		$success = 'CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->cancel( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getPaymentStatus() );
	}


	public function testQueryPaymentAuthorized()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment authorized test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}


	public function testWrongAuthorization()
	{
		$what = array(
			'VERSION' => '87.0',
			'SIGNATURE' => 'signature',
			'USER' => 'name',
			'PWD' => 'pw',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=wrong authorization test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->object->setCommunication( $com );

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->process( $this->order );
	}


	public function testSetPaymentStatusNone()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Payment\PayPalExpress' );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, [] ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentPending()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Payment\PayPalExpress' );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Pending', 'PENDINGREASON' => 'error' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentRefunded()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Payment\PayPalExpress' );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Refunded' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUND, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentCanceled()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Payment\PayPalExpress' );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Voided' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Payment\PayPalExpress' );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Invalid' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}
}


class TestPayPalExpress implements \Aimeos\MW\Communication\Iface
{
	private $rules = [];


	/**
	 * Adds rules to the communication object.
	 *
	 * @param array $what List of rules for the unit tests.
	 * @param string $error Error message if some of the tests fails.
	 * @param string $success Success message if all tests were passed.
	 */
	public function addRule( array $what, $error, $success )
	{
		$this->rules['set'] = $what;
		$this->rules['error'] = $error;
		$this->rules['success'] = $success;
	}


	/**
	 * Get rules of the communication object.
	 *
	 * @return array rules for internal check
	 */
	public function getRules()
	{
		return $this->rules;
	}


	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param mixed $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload )
	{
		if( !isset( $this->rules['set'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No rules for unit tests was set' ) );
		}

		if( !isset( $this->rules['error'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No error message for unit tests was set' ) );
		}

		if( !isset( $this->rules['success'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No success message for unit tests was set' ) );
		}

		$params = [];
		parse_str( $payload, $params );

		foreach( $this->rules['set'] as $key => $value )
		{
			if( $params[$key] != $value ) {
				return $this->rules['error'];
			}
		}

		return $this->rules['success'];
	}
}