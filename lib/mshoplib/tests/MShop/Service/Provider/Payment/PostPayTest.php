<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Payment\PostPay.
 */
class PostPayTest extends \PHPUnit_Framework_TestCase
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

		$this->object = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Payment\\PostPay' )
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
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => 'testurl' ) );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['payment.url-success'] );
	}


	public function testUpdateSync()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$order = $manager->createItem();

		$this->object->expects( $this->once() )->method( 'getOrder' )->will( $this->returnValue( $order ) );
		$this->object->updateSync( array( 'orderid' => -1 ) );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $order->getPaymentStatus() );
	}


	public function testIsImplemented()
	{
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
	}
}