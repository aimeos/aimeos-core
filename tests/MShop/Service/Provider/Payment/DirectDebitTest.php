<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class DirectDebitTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $ordServItem;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$this->ordServItem = \Aimeos\MShop::create( $context, 'order/service' )->create();
		$serviceItem = \Aimeos\MShop::create( $context, 'service' )->create();
		$serviceItem->setCode( 'test' );

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\DirectDebit::class )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->onlyMethods( ['save'] )
			->getMock();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( 0, count( $this->object->getConfigBE() ) );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => 'http://returnUrl' ) );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop::create( \TestHelper::context(), 'order' );
		$search = $orderManager->filter()->add( [
			'order.channel' => 'web',
			'order.statuspayment' => \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED
		] );

		$order = $orderManager->search( $search, ['order/address'] )
			->first( new \RuntimeException( 'No order found' ) );

		$config = $this->object->getConfigFE( $order );

		$this->assertArrayHasKey( 'directdebit.accountowner', $config );
		$this->assertArrayHasKey( 'directdebit.accountno', $config );
		$this->assertArrayHasKey( 'directdebit.bankcode', $config );
		$this->assertArrayHasKey( 'directdebit.bankname', $config );
		$this->assertEquals( 'Our Unittest', $config['directdebit.accountowner']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$config = array(
			'directdebit.accountowner' => 'test user',
			'directdebit.accountno' => '123456789',
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->object->checkConfigFE( $config );

		$expected = array(
			'directdebit.accountowner' => null,
			'directdebit.accountno' => null,
			'directdebit.bankcode' => null,
			'directdebit.bankname' => null,
		);

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$config = array(
			'directdebit.accountowner' => [],
			'directdebit.accountno' => [],
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'directdebit.accountowner', $result );
		$this->assertArrayHasKey( 'directdebit.accountno', $result );

		$this->assertIsString( $result['directdebit.accountowner'] );
		$this->assertIsString( $result['directdebit.accountno'] );
		$this->assertNull( $result['directdebit.bankcode'] );
		$this->assertNull( $result['directdebit.bankname'] );
	}


	public function testSetConfigFE()
	{
		$this->object->setConfigFE( $this->ordServItem, array( 'directdebit.accountno' => '123456' ) );

		$attrItem = $this->ordServItem->getAttributeItem( 'directdebit.accountno', 'payment' );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $attrItem );
		$this->assertEquals( 'XXX456', $attrItem->getValue() );

		$attrItem = $this->ordServItem->getAttributeItem( 'directdebit.accountno', 'payment/hidden' );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $attrItem );
		$this->assertEquals( '123456', $attrItem->getValue() );
	}


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop::create( \TestHelper::context(), 'order' )->create();
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $result->getStatusPayment() );
	}


	public function testIsImplemented()
	{
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
	}
}
