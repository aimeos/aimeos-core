<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2024
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
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->context->setUser( null );

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\OrderCheck::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\OrderCheck( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->willReturn( [] );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'ordercheck.total-number-min', $result );
		$this->assertArrayHasKey( 'ordercheck.limit-days-pending', $result );
	}


	public function testCheckConfigBETotal()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array( 'ordercheck.total-number-min' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBETotalFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array( 'ordercheck.total-number-min' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBELimit()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array( 'ordercheck.limit-days-pending' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['ordercheck.limit-days-pending'] );
	}


	public function testCheckConfigBELimitFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

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
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->willReturn( true );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotal()
	{
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->willReturn( map( [$mock->create()] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->willReturn( true );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotalNotEnough()
	{
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->willReturn( map() );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimit()
	{
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->willReturn( map() );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->willReturn( true );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimitTooMuch()
	{
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'search' )
			->willReturn( map( [$mock->create()] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}

}
