<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
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
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Example::class )
			->disableOriginalConstructor()->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Free( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', null );
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
		$basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}


	public function testIsAvailableNotZero()
	{
		$this->servItem->setConfig( array( 'free.show' => '1' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();
		$basket->getPrice()->setValue( '0.01' );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}


	public function testIsNotAvailableHidden()
	{
		$this->servItem->setConfig( array( 'free.show' => '0' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();

		$this->assertFalse( $this->object->isAvailable( $basket ) );
	}


	public function testIsAvailableHiddenNotZero()
	{
		$this->servItem->setConfig( array( 'free.show' => '0' ) );
		$basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();
		$basket->getPrice()->setValue( '0.01' );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $basket ) );
	}
}
