<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class NotTest extends \PHPUnit\Framework\TestCase
{
	private $provider;
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$item = \Aimeos\MShop::create( $context, 'service' )->create();

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();

		$this->provider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$context, $item] )
			->onlyMethods( ['isAvailable'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Not( $this->provider, $context, $item );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->provider, $this->order );
	}


	public function testIsAvailableFalse()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertFalse( $this->object->isAvailable( $this->order ) );
	}


	public function testIsAvailableTrue()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( false ) );
		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}
}
