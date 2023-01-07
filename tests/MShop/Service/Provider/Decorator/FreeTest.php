<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class FreeTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Example::class )
			->disableOriginalConstructor()->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Free( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'free.show', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'free.show' => '1' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['free.show'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'free.show' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['free.show'] );
	}


	public function testIsAvailable()
	{
		$this->servItem->setConfig( array( 'free.show' => '1' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}


	public function testIsAvailableNotZero()
	{
		$this->servItem->setConfig( array( 'free.show' => '1' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();
		$basket->getPrice()->setValue( '0.01' );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}


	public function testIsNotAvailableHidden()
	{
		$this->servItem->setConfig( array( 'free.show' => '0' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();

		$this->assertFalse( $this->object->isAvailable( $basket ) );
	}


	public function testIsAvailableHiddenNotZero()
	{
		$this->servItem->setConfig( array( 'free.show' => '0' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();
		$basket->getPrice()->setValue( '0.01' );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}
}
