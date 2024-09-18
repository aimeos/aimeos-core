<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2024
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
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );

		$search = $serviceManager->filter()->add( ['service.code' => 'paypalexpress'] );
		$this->serviceItem = $serviceManager->search( $search )->first( new \RuntimeException( 'No service item available' ) );

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class )
			->setConstructorArgs( [$this->context, $this->serviceItem] )
			->onlyMethods( ['send'] )
			->getMock();


		$orderManager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $orderManager->filter()->add( 'order.invoiceno', '==', 'UINV-003' );
		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];
		$this->order = $orderManager->search( $search, $ref )->first( new \RuntimeException( 'No order found' ) );

		$attr = \Aimeos\MShop::create( $this->context, 'order/service' )->createAttributeItem();
		$attr->setType( 'tx' )->setCode( 'TRANSACTIONID' )->setValue( '111111111' );
		$this->order->getService( 'payment', 0 )->setAttributeItem( $attr );


		$this->orderMock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( ['save', 'type'] )
			->getMock();

		$this->orderMock->method( 'type' )->willReturn( ['order'] );
		$this->orderMock->expects( $this->any() )->method( 'save' )->willReturnArgument( 0 );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $this->orderMock );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->serviceItem, $this->order );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 16, count( $result ) );

		foreach( $result as $key => $item ) {
			$this->assertInstanceOf( 'Aimeos\Base\Criteria\Attribute\Iface', $item );
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
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( '&ACK=Success&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&TOKEN=UT-99999999' );

		$helperForm = $this->object->process( $this->order );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $helperForm );
		$this->assertEquals( 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=UT-99999999', $helperForm->getUrl() );
		$this->assertEquals( 'POST', $helperForm->getMethod() );
		$this->assertEquals( [], $helperForm->getValues() );

		$attrs = [];
		foreach( $this->order->getService( 'payment', 0 )->getAttributeItems() as $attrItem ) {
			$attrs[$attrItem->getCode()] = $attrItem->getValue();
		}

		$this->assertEquals( 'UT-99999999', $attrs['TOKEN'] );
		$this->assertEquals( '111111111', $attrs['TRANSACTIONID'] );
	}


	public function testUpdateSync()
	{
		//DoExpressCheckout

		$params = array(
			'token' => 'UT-99999999',
			'PayerID' => 'PaypalUnitTestBuyer'
		);

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$request->expects( $this->once() )->method( 'getAttributes' )->willReturn( [] );
		$request->expects( $this->once() )->method( 'getParsedBody' )->willReturn( [] );
		$request->expects( $this->once() )->method( 'getQueryParams' )->willReturn( $params );

		$this->object->expects( $this->once() )->method( 'send' )->willReturn( '&TOKEN=UT-99999999&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725&PAYERID=PaypalUnitTestBuyer&TRANSACTIONID=111111110&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=' . $this->order->getId() );

		$result = $this->object->updateSync( $request, $this->order );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $result->getStatusPayment() );
	}


	public function testUpdatePush()
	{
		//IPN Call
		$price = $this->order->getPrice();
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

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();

		$request->expects( $this->once() )->method( 'getQueryParams' )->willReturn( $params );
		$response->expects( $this->once() )->method( 'withStatus' )
			->willReturn( $response )
			->with( $this->equalTo( 200 ) );

		$cmpFcn = function( $subject ) {
			$attrs = $subject->getService( 'payment', 0 )->getAttributeItems();
			$attrs = $attrs->col( 'order.service.attribute.value', 'order.service.attribute.code' );
			return $subject->getStatusPayment() === \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED
				&& $attrs['TRANSACTIONID'] === '111111111'
				&& $attrs['111111111'] === 'Completed';
		};

		$this->orderMock->expects( $this->once() )->method( 'save' )->with( $this->callback( $cmpFcn ) );
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'VERIFIED' );

		$result = $this->object->updatePush( $request, $response );
		$this->assertInstanceOf( \Psr\Http\Message\ResponseInterface::class, $result );
	}


	public function testRefund()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'REFUNDTRANSACTIONID=88888888&FEEREFUNDAMT=2.00&TOTALREFUNDAMT=24.00&CURRENCYCODE=EUR&REFUNDSTATUS=delayed&PENDINGREASON=echeck&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->refund( $this->order );

		$testData = array(
			'TRANSACTIONID' => '111111111',
			'REFUNDTRANSACTIONID' => '88888888'
		);

		$attributes = $this->order->getService( 'payment', 0 )->getAttributeItems();

		$attributeList = [];
		foreach( $attributes as $attribute ) {
			$attributeList[$attribute->getCode()] = $attribute;
		}

		foreach( $testData as $key => $value ) {
			$this->assertEquals( $attributeList[$key]->getValue(), $testData[$key] );
		}

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUND, $this->order->getStatusPayment() );
	}


	public function testCapture()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'AUTHORIZATIONID=112233&TRANSACTIONID=111111111&PARENTTRANSACTIONID=12212AD&TRANSACTIONTYPE=express-checkout&AMT=22.30&FEEAMT=3.33&PAYMENTSTATUS=Completed&PENDINGREASON=None&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->capture( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getStatusPayment() );
	}


	public function testQueryPaymentReceived()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Completed&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getStatusPayment() );
	}


	public function testQueryPaymentRefused()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Expired&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUSED, $this->order->getStatusPayment() );
	}


	public function testCancel()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->cancel( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getStatusPayment() );
	}


	public function testQueryPaymentAuthorized()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725' );

		$this->object->query( $this->order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->order->getStatusPayment() );
	}


	public function testWrongAuthorization()
	{
		$this->object->expects( $this->once() )->method( 'send' )->willReturn( '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=wrong authorization test method error' );

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->process( $this->order );
	}


	public function testSetPaymentStatusNone()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setStatusPayment' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, [] ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getStatusPayment() );
	}


	public function testSetPaymentPending()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setStatusPayment' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Pending', 'PENDINGREASON' => 'error' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $this->order->getStatusPayment() );
	}


	public function testSetPaymentRefunded()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setStatusPayment' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Refunded' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_REFUND, $this->order->getStatusPayment() );
	}


	public function testSetPaymentCanceled()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setStatusPayment' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Voided' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $this->order->getStatusPayment() );
	}


	public function testSetPaymentInvalid()
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Payment\PayPalExpress::class );
		$method = $class->getMethod( 'setStatusPayment' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $this->order, array( 'PAYMENTSTATUS' => 'Invalid' ) ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->order->getStatusPayment() );
	}
}
