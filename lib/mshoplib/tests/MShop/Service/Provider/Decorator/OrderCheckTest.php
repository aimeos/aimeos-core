<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_OrderCheck.
 */
class MShop_Service_Provider_Decorator_OrderCheckTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->context->setUserId( null );

		$servManager = MShop_Service_Manager_Factory::createManager( $this->context );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_OrderCheck' )
			->disableOriginalConstructor()->getMock();

		$this->basket = MShop_Order_Manager_Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new MShop_Service_Provider_Decorator_OrderCheck( $this->context, $this->servItem, $this->mockProvider );
	}


	protected function tearDown()
	{
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_StandardMock', null );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'ordercheck.total-number-min', $result );
		$this->assertArrayHasKey( 'ordercheck.limit-days-pending', $result );
	}


	public function testCheckConfigBETotal()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'ordercheck.total-number-min' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBETotalFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'ordercheck.total-number-min' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBELimit()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'ordercheck.limit-days-pending' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['ordercheck.limit-days-pending'] );
	}


	public function testCheckConfigBELimitFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'ordercheck.limit-days-pending' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['ordercheck.limit-days-pending'] );
	}


	public function testIsAvailableNoUserId()
	{
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoConfig()
	{
		$this->context->setUserId( 1 );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotal()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'classes/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array( $mock->createItem() ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotalNotEnough()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'classes/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array() ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimit()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'classes/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array() ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimitTooMuch()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'classes/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array( $mock->createItem() ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}

}