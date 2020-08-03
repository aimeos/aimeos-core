<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class PayPalExpressTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $serviceItem;
	private $orderMock;
	private $order;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'paypalexpress' ) );

		$serviceItems = $serviceManager->searchItems( $search )->toArray();

		if( ( $this->serviceItem = reset( $serviceItems ) ) === false ) {
			throw new \RuntimeException( 'No paypalexpress service item available' );
		}

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class )
			->setConstructorArgs( [$this->context, $this->serviceItem] )
			->setMethods( ['send'] )
			->getMock();


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );

		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search )->toArray();

		if( ( $this->order = reset( $orderItems ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No Order found with statuspayment "%1$s" and type "%2$s"', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::TYPE_WEB ) );
		}


		$this->orderMock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$this->orderMock->expects( $this->any() )->method( 'saveItem' )->will( $this->returnArgument( 0 ) );

		$this->context->getConfig()->set( 'mshop/order/manager/name', 'MockPayPal' );
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\MockPayPal', $this->orderMock );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->serviceItem );
		unset( $this->order );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 15, count( $result ) );

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

		$this->assertEquals( 15, count( $result ) );
		$this->assertEquals( null, $result['paypalexpress.ApiUsername'] );
		$this->assertEquals( null, $result['paypalexpress.AccountEmail'] );
		$this->assertEquals( null, $result['paypalexpress.ApiPassword'] );
		$this->assertEquals( null, $result['paypalexpress.ApiSignature'] );
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
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( '&ACK=Success&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&TOKEN=UT-99999999' )
		);

		$helperForm = $this->object->process( $this->order );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId() );

		$attributes = $refOrderBase->getService( 'payment', 0 )->getAttributeItems();

		$attributeList = [];
		foreach( $attributes as $attribute ) {
			$attributeList[$attribute->getCode()] = $attribute;
		}

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $helperForm );
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

		$params = array(
			'token' => 'UT-99999999',
			'PayerID' => 'PaypalUnitTestBuyer'
		);

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$request->expects( $this->once() )->method( 'getAttributes' )->will( $this->returnValue( [] ) );
		$request->expects( $this->once() )->method( 'getParsedBody' )->will( $this->returnValue( [] ) );
		$request->expects( $this->once() )->method( 'getQueryParams' )->will( $this->returnValue( $params ) );

		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( '&TOKEN=UT-99999999&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725&PAYERID=PaypalUnitTestBuyer&TRANSACTIONID=111111110&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=' . $this->order->getId() )
		);

		$result = $this->object->updateSync( $request, $this->order );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $result->getPaymentStatus() );
	}


	public function testUpdatePush()
	{
		//IPN Call
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$price = $orderBaseManager->getItem( $this->order->getBaseId() )->getPrice();
		$amount = $price->getValue() + $price->getCosts();

		$params = array(
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
			'111111110' => 'Pending',
			'111111111' => 'Completed'
		);

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();

		$request->expects( $this->once() )->method( 'getQueryParams' )->will( $this->returnValue( $params ) );
		$response->expects( $this->once() )->method( 'withStatus' )
			->will( $this->returnValue( $response ) )
			->with( $this->equalTo( 200 ) );

		$cmpFcn = function( $subject ) {
			return $subject->getPaymentStatus() === \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED;
		};

		$this->orderMock->expects( $this->once() )->method( 'saveItem' )->with( $this->callback( $cmpFcn ) );
		$this->object->expects( $this->once() )->method( 'send' )->will( $this->returnValue( 'VERIFIED' ) );

		$result = $this->object->updatePush( $request, $response );
		$this->assertInstanceOf( \Psr\Http\Message\ResponseInterface::class, $result );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );
		$attributes = $refOrderBase->getService( 'payment', 0 )->getAttributeItems();
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
	}


	public function testRefund()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'REFUNDTRANSACTIONID=88888888&FEEREFUNDAMT=2.00&TOTALREFUNDAMT=24.00&CURRENCYCODE=EUR&REFUNDSTATUS=delayed&PENDINGREASON=echeck&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->refund( $this->order );

		$testData = array(
			'TOKEN' => 'UT-99999999',
			'TRANSACTIONID' => '111111111',
			'REFUNDTRANSACTIONID' => '88888888'
		);

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$refOrderBase = $orderBaseManager->load( $this->order->getBaseId() );
		$attributes = $refOrderBase->getService( 'payment', 0 )->getAttributeItems();

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
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'AUTHORIZATIONID=112233&TRANSACTIONID=111111111&PARENTTRANSACTIONID=12212AD&TRANSACTIONTYPE=express-checkout&AMT=22.30&FEEAMT=3.33&PAYMENTSTATUS=Completed&PENDINGREASON=None&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->capture( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getPaymentStatus() );
	}


	public function testQueryPaymentReceived()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Completed&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getPaymentStatus() );
	}


	public function testQueryPaymentRefused()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Expired&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUSED, $this->order->getPaymentStatus() );
	}


	public function testCancel()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->cancel( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getPaymentStatus() );
	}


	public function testQueryPaymentAuthorized()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' )
		);

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}


	public function testWrongAuthorization()
	{
		$this->object->expects( $this->once() )->method( 'send' )->will(
			$this->returnValue( '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=wrong authorization test method error' )
		);

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->process( $this->order );
	}


	public function testSetPaymentStatusNone()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, [] ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentPending()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Pending', 'PENDINGREASON' => 'error' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentRefunded()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Refunded' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUND, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentCanceled()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Voided' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getPaymentStatus() );
	}


	public function testSetPaymentInvalid()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setPaymentStatus' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Invalid' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getPaymentStatus() );
	}
}
