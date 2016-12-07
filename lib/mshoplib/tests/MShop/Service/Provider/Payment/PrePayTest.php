<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Payment\PrePay.
 */
class PrePayTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
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


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( 4, count( $this->object->getConfigBE() ) );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'payment.url-success' => 'http://returnUrl'
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['payment.url-success'] );
	}


	public function testUpdateSync()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$order = $manager->createItem();

		$this->object->expects( $this->once() )->method( 'getOrder' )->will( $this->returnValue( $order ) );
		$this->object->updateSync( array( 'orderid' => -1 ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $order->getPaymentStatus() );
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