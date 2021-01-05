<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class OrderCheckTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->context->setUserId( null );

		$servManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\OrderCheck::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )
			->getSubManager( 'base' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\OrderCheck( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', null );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'ordercheck.total-number-min', $result );
		$this->assertArrayHasKey( 'ordercheck.limit-days-pending', $result );
	}


	public function testCheckConfigBETotal()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.total-number-min' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBETotalFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.total-number-min' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBELimit()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.limit-days-pending' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['ordercheck.limit-days-pending'] );
	}


	public function testCheckConfigBELimitFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.limit-days-pending' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['ordercheck.limit-days-pending'] );
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
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( map( [$mock->create()] ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotalNotEnough()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( map() ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimit()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( map() ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimitTooMuch()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( map( [$mock->create()] ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}

}
