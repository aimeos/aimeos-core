<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class PrePayTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$serviceManager = \Aimeos\MShop::create( $context, 'service' );

		$serviceItem = $serviceManager->create();
		$serviceItem->setCode( 'test' );

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\PrePay::class )
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


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop::create( \TestHelper::context(), 'order' )->create();
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $result->getStatusPayment() );
	}


	public function testIsImplemented()
	{
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
	}


	public function testCancel()
	{
		$orderItem = \Aimeos\MShop::create( \TestHelper::context(), 'order' )->create();

		$this->object->cancel( $orderItem );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $orderItem->getStatusPayment() );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'order/service' )->create();
		$this->object->setConfigFE( $item, array( 'test.code' => 'abc', 'test.number' => 123 ) );

		$this->assertEquals( 2, count( $item->getAttributeItems() ) );
		$this->assertEquals( 'abc', $item->getAttribute( 'test.code', 'payment' ) );
		$this->assertEquals( 123, $item->getAttribute( 'test.number', 'payment' ) );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.code', 'payment' )->getType() );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.number', 'payment' )->getType() );
	}
}
