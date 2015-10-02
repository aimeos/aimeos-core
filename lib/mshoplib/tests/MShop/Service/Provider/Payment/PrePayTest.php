<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Payment_PrePay.
 */
class MShop_Service_Provider_Payment_PrePayTest extends PHPUnit_Framework_TestCase
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
		$context = TestHelper::getContext();
		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );

		$serviceItem = $serviceManager->createItem();
		$serviceItem->setCode( 'test' );

		$this->object = $this->getMockBuilder( 'MShop_Service_Provider_Payment_PrePay' )
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


	public function testProcess()
	{
		// Currently does nothing.
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );

		$this->object->process( $manager->createItem() );
	}


	public function testIsImplemented()
	{
		$this->assertTrue( $this->object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL ) );
		$this->assertFalse( $this->object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) );
	}


	public function testCancel()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderItem = $manager->createItem();
		$this->object->cancel( $orderItem );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_CANCELED, $orderItem->getPaymentStatus() );
	}


	public function testSetConfigFE()
	{
		$item = MShop_Factory::createManager( TestHelper::getContext(), 'order/base/service' )->createItem();
		$this->object->setConfigFE( $item, array( 'test.code' => 'abc', 'test.number' => 123 ) );

		$this->assertEquals( 2, count( $item->getAttributes() ) );
		$this->assertEquals( 'abc', $item->getAttribute( 'test.code', 'payment' ) );
		$this->assertEquals( 123, $item->getAttribute( 'test.number', 'payment' ) );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.code', 'payment' )->getType() );
		$this->assertEquals( 'payment', $item->getAttributeItem( 'test.number', 'payment' )->getType() );
	}
}