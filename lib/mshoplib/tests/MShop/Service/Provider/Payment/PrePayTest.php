<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class PrePayTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $context );

		$serviceItem = $serviceManager->createItem();
		$serviceItem->setCode( 'test' );

		$this->object = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Payment\\PrePay' )
			->setMethods( array( 'getOrder', 'getOrderBase', 'saveOrder', 'saveOrderBase' ) )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();
	}


	protected function tearDown()
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
		$orderItem = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Iface', $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $result->getPaymentStatus() );
	}


	public function testIsImplemented()
	{
		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
	}


	public function testCancel()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderItem = $manager->createItem();
		$this->object->cancel( $orderItem );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $orderItem->getPaymentStatus() );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'order/base/service' )->createItem();
		$this->object->setConfigFE( $item, array( 'test.code' => 'abc', 'test.number' => 123 ) );

		$this->assertEquals( 2, count( $item->getAttributes() ) );
		$this->assertEquals( 'abc', $item->getAttribute( 'test.code', 'payment' ) );
		$this->assertEquals( 123, $item->getAttribute( 'test.number', 'payment' ) );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.code', 'payment' )->getType() );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.number', 'payment' )->getType() );
	}
}